<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\CategoryRequest;
class CategoryController extends Controller
{
  
use AuthorizesRequests;
  public function index() {
  $categories = Category::visibleTo(auth('sanctum')->user())->with(['projects.articles'])
  ->latest()->get();
    return CategoryResource::collection($categories);
  }
  public function store(CategoryRequest $request)
  {
    $this->authorize('create', [Category::class]);
    $category = Category::create($request->validated());

    return (new CategoryResource($category))->response()->setStatusCode(201);

    }

  public function show(Category $category) {
  $this->authorize('view', $category);  
    return new CategoryResource($category->load('projects.articles'));
  
  }

  public function update(CategoryUpdateRequest $request, Category $category) {
    $this->authorize('update', $category);
    $category->update($request->validated());

    return new CategoryResource($category);
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
    return CategoryResource::collection(Category::withCount('projects')->get());
    }
       
}