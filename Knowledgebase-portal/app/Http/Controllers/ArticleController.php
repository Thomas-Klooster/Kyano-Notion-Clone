<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleUpdateRequest;
use Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Attachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Project;
use App\Models\Article;
use App\Models\Feedback;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\FeedbackRequest;
class ArticleController extends Controller
{

    use AuthorizesRequests;

    private function storeAttachmentsForArticle(Article $article, array $files): void
    {
        foreach ($files as $file) {
            $path = Storage::disk('public')->put('attachments', $file);

            Attachment::create([
                'article_id' => $article->id,
                'path' => $path,
                'mime' => $file->getMimeType(),
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
        }
    }

    public function index() {
     $this->authorize('viewAny', Article::class);        
     $articles = Article::visibleTo(auth('sanctum')->user())
     ->where('status', 'published')->latest()->get();
     return ArticleResource::collection($articles);
     }


   public function store(ArticleRequest $request) {
    $this->authorize('create', [Article::class, $request->project_id]);
    $data = $request->validated();
    $article = Article::create(Arr::except($data, ['attachments']));

    $article->syncTags($request->tags ?? []);

    if ($request->hasFile('attachments')) {
        $this->storeAttachmentsForArticle($article, $request->file('attachments'));
    }

    return (new ArticleResource($article->load(['attachments', 'tags'])))->response()->setStatusCode(201);
}

public function show(Article $article)
{
    $this->authorize('view', $article);
    return new ArticleResource(
        $article->load(['tags', 'project', 'categories', 'attachments'])
    );
}
    public function update(ArticleUpdateRequest $request, Article $article) {

        $this->authorize('update', $article);
        $article->update($request->validated());

        return new ArticleResource($article->load(['tags', 'project', 'category', 'attachments']));
    }

    public function storeAttachments(Request $request, Article $article) {
        $this->authorize('update', $article);
        $data = $request->validate([
            'attachments' => ['required', 'array', 'min:1'],
            'attachments.*' => ['file', 'mimes:jpg,xlsx,jpeg,png,pdf,doc,docx,webm', 'max:10240'],
        ]);

        $this->storeAttachmentsForArticle($article, $data['attachments']);
        return new ArticleResource($article->load(['attachments', 'tags', 'project', 'category']));
    }

    public function deleteAttachment(Article $article, Attachment $attachment) {
        $this->authorize('delete', $article);
        $attachment->delete();
        return response()->json(['delete' => true]);
    }

    public function destroy(Article $article) {
        $this->authorize('delete', $article);
        $article->delete();
        return response()->json(['delete' => true]);
        }



    public function showPublished(Project $project, Article $article) {
    abort_if($article->project_id !== $project->id, 404);
    abort_if($article->status !== 'published', 403);
    abort_if($article->visibility !== 'public', 403);

    return new ArticleResource($article->load(['project', 'tags', 'category', 'attachments']));
  
  }

    public function search(Project $project, Request $request) {
        $this->authorize('view', $project);

      $keyword = $request->input('keyword');
      if (!$keyword) return response()->json([]);

     $articles = Article::visibleTo(auth('sanctum')->user())
     ->with(['project', 'category', 'attachments'])
    ->where('project_id', $project->id)
      ->where('status', 'published') 
        ->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('content', 'like', "%{$keyword}%")
              ->orWhere('summary', 'like', "%{$keyword}%");
        })->get();
        return ArticleResource::collection($articles);
   }

    public function projectArticles(Project $project) {
    $this->authorize('view', $project);
    $articles = Article::visibleTo(auth('sanctum')->user())
        ->with(['project', 'category', 'attachments'])
        ->where('project_id', $project->id)
        ->get();
         return ArticleResource::collection($articles);
        }
    public function storeFeedback(FeedbackRequest $request, Article $article) {
    $this->authorize('view', $article);
    $data = $request->validated();
    $feedback = $article->feedbacks()->create([
        'user_id' => auth()->id(),
        'helpful' => $data['helpful'],
        'feedback' => $data['feedback'],
        'is_read' => false,
    ]);
    return response()->json($feedback, 201);
}


    public function adminFeedbacks(Article $article) {
        $this->authorize('viewAny', Article::class);
        $feedbacks = $article->feedbacks()->with('user')->latest()->get();
        return response()->json($feedbacks);
    }

    public function adminAllFeedbacks()
    {
        $this->authorize('viewAny', Article::class);

        $feedbacks = Feedback::query()
            ->with([
                'user:id,name,email',
                'article:id,title,slug,status,updated_at,project_id,workspace_id',
                'article.project:id,name',
                'article.workspace:id,name',
            ])
            ->latest()
            ->get();

        return response()->json($feedbacks);
    }

    public function markFeedbackAsRead(Request $request, Feedback $feedback)
    {
        $this->authorize('viewAny', Article::class);

        $data = $request->validate([
            'is_read' => ['sometimes', 'boolean'],
        ]);

        $feedback->update([
            'is_read' => $data['is_read'] ?? true,
        ]);

        return response()->json($feedback->fresh());
    }

    public function destroyFeedback(Feedback $feedback)
    {
        $this->authorize('viewAny', Article::class);

        $feedback->delete();

        return response()->json(['deleted' => true]);
    }
    
    public function AdminIndex(Request $request)
    {
        $query = Article::visibleTo(auth('sanctum')->user())
        ->with(['project', 'category', 'attachments', 'tags']);

        if ($request->user_id) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

          return ArticleResource::collection($query->latest()->get());
      }
}
