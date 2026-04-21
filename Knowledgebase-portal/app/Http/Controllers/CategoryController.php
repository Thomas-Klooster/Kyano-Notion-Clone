<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\CategoryRequest;
class CategoryController extends Controller
{
  
use AuthorizesRequests;
  public function index() {
  $categories = Category::visibleTo(auth()->user())->with(['projects.articles'])
  ->latest()->get();
        return response()->json($categories);
  }
  public function store(CategoryRequest $request)
  {
    $this->authorize('create', [Category::class]);
    $data = $request->validated();
    return Category::create($data);      
    }

  public function show(Category $category) {
  $this->authorize('view', $category);  
  return $category->load('projects.articles');
  
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