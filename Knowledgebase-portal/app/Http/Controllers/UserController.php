<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        return response()->json(User::query()->latest('id')->get());
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return response()->json($user);
    }

    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $payload = $request->validated();
        if (isset($payload['password'])) {
            $payload['password'] = bcrypt($payload['password']);
        }

        $user = User::create($payload);

        return response()->json($user->fresh(), 201);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $payload = $request->validated();
        if (isset($payload['password'])) {
            $payload['password'] = bcrypt($payload['password']);
        }

        $user->update($payload);

        return response()->json($user->fresh());
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'Gebruiker is verwijderd.',
        ]);
    }
}
