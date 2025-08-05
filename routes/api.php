<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Invoice;

Route::get('/user', function (Request $request) {
    $user = $request->user();

    if (! $user instanceof \App\Models\User || ! $user->hasRole('Administrador')) {
        return response()->json(['message' => 'No autorizado.'], 403);
    }

    return User::all();
})->middleware('auth:sanctum');

Route::get('/invoice', function (Request $request) {
    return Invoice::with([ 'items'])
        ->where('client_id', $request->user()->id)
        ->latest()
        ->get();
})->middleware('auth:sanctum');