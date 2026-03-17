<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Attachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Project;
use App\Models\Article;
use App\Models\Workspace;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\FeedbackRequest;
class ArticleController extends Controller
{

    use AuthorizesRequests;
    public function index() {
        return
        Article::visibleTo(auth()->user())->latest()->get();
    }

    public function store(ArticleRequest $request)     {
    $data = $request->validated();

     return Article::create($data);
    }

    public function storeAttachment(Request $request): JsonResponse
    {   
        $data = $request->validated();
                
        $file = Storage::put('attachments', $data['file']);
        $attachment = Attachment::create(
            [
                'article_id' => $data['article_id'],
                'path' => $file,
                'mime' => $data['file']->getMimeType(),
                'original_name' => $data['file']->getClientOriginalName(),
                'size' => $data['file']->getSize()
            ],
        );
        return new JsonResponse($attachment);
    }

    public function show(Article $article) { 
    $this->authorize('view', $article);
    return $article->load(['project', 'category']);
    }

    public function update(ArticleUpdateRequest $request, Article $article) {

        $this->authorize('update', $article);
        $article->update($request->validated());

        return $article;
    }

    public function destroy(Article $article) {
        $this->authorize('delete', $article);
        $article->delete();
        return response()->json(['delete' => true]);
        }


    public function ProjectByIndex(Project $project) {
        
    $this->authorize('view', $project);

    $articles = $project->articles()->where('status', 'public')->get();
    return response()->json($articles);
    }


    public function showPublished(Project $project, Article $article)
    {
    abort_if($article->project_id !== $project->id, 404);
    abort_if($article->status !== 'public', 403);
    return response()->json($article);
}

    public function search(Project $project, Request $request)
    {
        
     $query = $request->input('keyword');
     if (!$query) {
        return response()->json([]);
     }
     $articles = $project->articles()
      ->where('status', 'public') 
        ->where(function ($keyword) use ($query) {
            $keyword->where('title', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%");
        })->get();

    return response()->json($articles);
}
    public function projectArticles(Project $project)
    {
        $query = $project->article();
         if (auth()->user()->role === 'admin') {
            $query->whereIn('status', ['draft', 'published', 'archived'])
                ->whereIn('visibility', ['public', 'private']);
        } else {
            $query->where('status', 'published' )
            ->where('visibility', 'public'); 
        }
        return $query->get();
    }

    public function storeFeedback(FeedbackRequest $request, Article $article) {
    $data = $request->validated();
    
    $feedback = $article->feedbacks()->updateOrCreate(
        ['user_id' => auth()->id()],    
        [
            'helpful' => $data['helpful'],
            'comment' => $data['comment'],

            ]);
            
    return response()->json($feedback, 201);
}

    public function AdminIndex(Request $request) {
        $query = Article::with(['category', 'project', 'article']);
        if ($request->user_id) {
        $query->where('user_id', $request->user_id);
         };

         return $query->latest()->get();
    }
}

