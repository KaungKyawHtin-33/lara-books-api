<?php

use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\Book\BookController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Genre\GenreController;
use App\Http\Controllers\Publisher\PublisherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/genres', GenreController::class);
Route::apiResource('/publishers', PublisherController::class);
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/authors', AuthorController::class);
Route::apiResource('/books', BookController::class);
