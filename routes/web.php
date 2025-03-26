<?php

use App\Http\Controllers\CrawlController;
use App\Http\Controllers\IndexPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexPageController::class, 'index'])->name('index');

//Route::get('/crawl', [CrawlController::class, 'index'])->name('crawl');
