<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Models\User;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/newPassword', [AuthController::class, 'newPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', fn(Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::get('/workspace/invite/accept', [WorkspaceController::class, 'acceptInvite'])->name('workspace.invite.accept');
    // Klant: kan alleen lezen binnen eigen klantomgeving + (optioneel) feedback geven.
    //Ziet eigen projecten
    //Kan artikelen lezen (alleen published)
    //Zoekbalk binnen project (titel + content)
    //(Optioneel) “Was dit nuttig?” + feedback tekstveld of met emojis
    Route::get('projects', [ProjectsController::class, 'myProjects']);
    Route::get('projects/{project}', [ProjectsController::class, 'show']);
    Route::get('projects/{project}', [ProjectsController::class, 'ProjectByIndex']);
    Route::get('/projects/{project}/articles/search', [ArticleController::class, 'search']);
    Route::get('/projects/{project}/articles/{article}', [ArticleController::class, 'showPublished']);
    Route::post('/articles/{article}/feedback', [ArticleController::class, 'storeFeedback']);
    
    });


    Route::middleware(['auth:sanctum', 'checkrole:admin'])->prefix('admin')->group(function () {
    Route::get('/test', function () {
            return response()->json(['message' => 'admin.']);
        });

        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        /* --------------------------Admin CRUD-------------------------- */

        Route::apiResource('projects', ProjectsController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('articles', ArticleController::class);
        Route::apiResource('workspaces', WorkspaceController::class);
        Route::post('/workspaces/{workspace}/invite', [WorkspaceController::class, 'invite']);
        Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceController::class, 'removeMember']);
        Route::post('/articles/attachment', [ArticleController::class, 'storeAttachment']);
        Route::get('/users/{id}',fn($id) => response()->json(User::findOrFail($id)));
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });