<?php

use App\Http\Controllers\YoutubeToMP3DownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [YoutubeToMP3DownloadController::class, 'index']);
Route::post('/', [YoutubeToMP3DownloadController::class, 'search']);
Route::get('/download', [YoutubeToMP3DownloadController::class, 'download']);