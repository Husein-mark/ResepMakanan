<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.search');
Route::get('/recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show')->whereNumber('id');
Route::get('/saved', [RecipeController::class, 'saved'])->name('recipes.saved');
