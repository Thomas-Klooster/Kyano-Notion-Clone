<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{

    use AuthorizesRequests;
    
    public function index() {
      $this->authorize('viewAny', User::class);
      $users = User::all();
      return response()->json($users);
    }
    public function destroy($id) {
     $user = User::findOrFail($id);
     $this->authorize('delete', $user);
     $user->delete();

    return response()->json([
    'message' => 'Gebruiker is verwijderd.'
    ]);
  }
    public function show($id) {    
      $this->authorize('view', User::class);
      $user = User::findOrFail($id);
      return response()->json($user);
    }

    public function store(UserRequest $request) {
    $this->authorize('create', User::class);
    $user = User::create($request->validated());
    return response()->json($user, 201);
}

    public function update(UserRequest $request, User $user) {
      $this->authorize('update', $user);
      $user->update($request->validated());
      return response()->json($user);
    }

}

