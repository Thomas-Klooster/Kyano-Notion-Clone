<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/reset-password/session', [AuthController::class, 'resetPasswordSession']);
Route::post('/reset-password', [AuthController::class, 'newPassword']);
// Route::post('/newPassword', [AuthController::class, 'newPassword']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn(Request $request) => $request->user());
    Route::patch('/me', function (Request $request) {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'company' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data);

        return response()->json($user->fresh());
    });
    Route::post('/change-password', [AuthController::class, 'resetPassword']);

    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show']);
    Route::get('projects', [ProjectsController::class, 'myProjects']);
    Route::get('/projects/{project}', [ProjectsController::class, 'show']);    
    Route::get('/projects/{project}/articles/search', [ArticleController::class, 'search']);
    Route::get('/projects/{project}/articles/{article}', [ArticleController::class, 'showPublished']);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::post('/articles/{article}/feedback', [ArticleController::class, 'storeFeedback']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('/workspace/invite/accept', [WorkspaceController::class, 'acceptInvite'])
        ->name('workspace.invite.accept');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectsController::class)->except(['index', 'show']); 
    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
    Route::apiResource('workspaces', WorkspaceController::class)->except(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::post('/workspaces/{workspace}/invite', [WorkspaceController::class, 'invite']);
    Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceController::class, 'removeMember']);
});
    Route::middleware(['auth:sanctum', 'checkrole:admin'])->prefix('admin')->group(function () {        
        
        /* --------------------------Admin CRUD-------------------------- */
        Route::apiResource('projects', ProjectsController::class)->except(['AdminIndex']);
        Route::apiResource('categories', CategoryController::class)->except(['AdminIndex']);
        Route::apiResource('articles', ArticleController::class)->except(['AdminIndex']);
        Route::post('/articles/{article}/attachments', [ArticleController::class, 'storeAttachments']);
        Route::get('/feedbacks', [ArticleController::class, 'adminAllFeedbacks']);
        Route::get('/articles/{article}/feedbacks', [ArticleController::class, 'adminFeedbacks']);
        Route::patch('/feedbacks/{feedback}/read', [ArticleController::class, 'markFeedbackAsRead']);
        Route::delete('/feedbacks/{feedback}', [ArticleController::class, 'destroyFeedback']);
        Route::apiResource('workspaces', WorkspaceController::class);
        Route::post('/workspaces/{workspace}/members', [WorkspaceController::class, 'addMember']);
        Route::get('/workspaces/{workspace}/available-users', [WorkspaceController::class, 'availableUsers']);
        Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceController::class, 'removeMember']);
        Route::apiResource('users', UserController::class);
       
        // Route::post('/articles/attachment', [ArticleController::class, 'store']);
        // Route::get('/users/{id}',fn($id) => response()->json(User::findOrFail($id)));
        // Route::post('/users', [UserController::class, 'store']);
        // Route::put('/users/{id}', [UserController::class, 'update']);
        // Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
