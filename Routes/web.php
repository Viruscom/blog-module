<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Blog\Http\Controllers\BlogController;
use Modules\Blog\Http\Controllers\FrontendController;

Route::prefix('blog')->group(function() {
    Route::get('/', [FrontendController::class, 'index']);
});

/* Admin */
Route::group(['prefix' => 'admin'], static function () {
    Route::group(['prefix' => 'blog'], static function () {
        Route::get('/', [BlogController::class, 'index'])->name('blog');
        Route::get('/create', [BlogController::class, 'create'])->name('blog.create');
        Route::post('/store', [BlogController::class, 'store'])->name('blog.store');
        Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
        Route::post('/{id}/update', [BlogController::class, 'update'])->name('blog.update');
        Route::delete('/{id}/delete', [BlogController::class, 'delete'])->name('blog.delete');
        Route::get('/{id}/show', [BlogController::class, 'show'])->name('blog.show');
        Route::get('/active/{id}/{active}', [BlogController::class, 'changeActiveStatus'])->name('blog.changeStatus');
    });
});
