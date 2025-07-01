<?php

use Illuminate\Support\Facades\Route;
use Mydnic\FilamentFileBrowser\Http\Controllers\FileBrowserController;

Route::name('filament-file-browser.')->group(function () {
    Route::get('/download-file', [FileBrowserController::class, 'downloadFile'])
        ->name('download-file');
    
    Route::get('/download-zip/{path}', [FileBrowserController::class, 'downloadZip'])
        ->name('download-zip');
});
