<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
  public function index() {
    return
    Category::withCount('articles')->get();
  }

  // public function AdminIndex(Request $request) {
  //     $query = Category::where(['projects', 'articles']);
  //     if ($request->user_id) {
  //       $query->where('user_id', $request->user_id);        
  //     }
  //     return $query->get();
  // }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => 'required',
      'slug' => 'unique:categories,slug'
    ]);

      $category = Category::create($data);

      // dd($data);
      return response()->json($category, 201);
      
    }

  public function show(Category $category) {
    return $category->load('articles');
  }

  public function update(Request $request, Category $category)
  {
    $data = $request->validate([
      'name' => 'sometimes|required',
      'slug' => 'sometimes|unique:categories,slug,'
      . $category->id,
    ]);

    $category->update($data);
    return $category;
  }
  
  public function destroy(Category $category) {
    $category->delete();
    return response()->json([
      'message' => 'Deleted'
    ]);
  }
}