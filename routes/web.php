<?php

use App\Http\Controllers\TorrentController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TorrentController::class, 'index'])->name('dashboard');
    Route::post('torrent/store', [TorrentController::class, 'store'])->name('torrent.store');
    Route::delete('torrent/destroy/{torrent}', [TorrentController::class, 'destroy'])->name('torrent.destroy');
});

require __DIR__.'/auth.php';
