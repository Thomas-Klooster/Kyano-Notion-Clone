<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\CategoryRequest;
class CategoryController extends Controller
{
  
use AuthorizesRequests;
// all
  public function index(Workspace $workspace) {
    $this->authorize('viewAny', Category::class);
    $categories = Category::where('workspace_id', $workspace->id)
        ->with('projects')->latest()->get();
        
        return response()->json($categories);
    }

public function store(CategoryRequest $request, Workspace $workspace)
{
    $this->authorize('create', Category::class);
    abort_unless($workspace->members()->where('user_id',
    auth()->id())->exists(), 403);
    $data = array_merge($request->validated(), [
    'workspace_id' => $workspace->id,
    ]);
    return Category::create($data);
}
    // single
  public function show(Category $category) {
  $this->authorize('view', $category);
  return $category->load('projects');
  
  }

  public function update(CategoryUpdateRequest $request, Category $category)
{
    $this->authorize('update', $category);

    $data = $request->validated();
    $category->update($data);
    return $category;
}
  public function destroy(Category $category)
  {
    $this->authorize('delete', Category::class);
    $category->delete();
    return response()->json([
      'message' => 'Verwijderd.'
    ]);
  }

  public function AdminIndex() {
    return Category::withCount('projects')->get();
    }


    
}