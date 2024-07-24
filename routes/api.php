<?php

use App\Http\Controllers\TacheController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/current-user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// register using sanctum
Route::post('register', function(Request $request) {
    $validators = validator($request->all(), [
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|confirmed'
    ]);
    if ($validators->fails()) {
        return response()->json([
            'error' => true,
            'validation' => true,
            'errors' => $validators->errors()
        ], 400);
    }
    $user = new User($request->only(['name', 'email','password']));
    // $user->password = bcrypt($user->password);
    try {
        $user->saveOrFail();
    } catch (Throwable $th) {
        return response()->json([
            'error' => true,
            'validation' => false,
            'message' => $th->getMessage()
        ], 500);
    }
    return response()->json([
        'error' => false,
        'user' => $user
    ], 201);
});

// login using sanctum
Route::post('login', function(Request $request) {
    $validators = validator($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string'
    ]);
    if ($validators->fails()) {
        return response()->json([
            'error' => true,
            'validation' => true,
            'errors' => $validators->errors()
        ], 400);
    }
    if (!auth()->attempt($request->only(['email', 'password']))) {
        return response()->json([
            'error' => true,
            'validation' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }
    $user = auth()->user();
    $token = $user->createToken('auth_token')->plainTextToken;
    $user->makeVisible(['id','email_verified_at']);
    return response()->json([
        'error' => false,
        'user' => $user,
        'token' => $token
    ], 200);
});

// sanctum secure routes
Route::middleware('auth:sanctum')->group(function() {
    // route necessitant une authentification pour accéder
    // route group pour les taches
    Route::prefix('taches')->group(function() {
        Route::post('', [TacheController::class, 'store']);
        Route::post('multiple', [TacheController::class, 'storeMultiple']);
        Route::get('', [TacheController::class, 'index']);
        Route::get('my', [TacheController::class, 'myTaches']);
        Route::get('{tache:uid}', [TacheController::class, 'show']);
        Route::put('{uid}/done', [TacheController::class, 'terminer']);
        Route::put('{tache:uid}', [TacheController::class, 'update']);
        Route::delete('{uid}', [TacheController::class, 'destroy']);
    });
    
});



