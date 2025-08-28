<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportPdfController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->get('/reports/{report}/pdf', [ReportPdfController::class, 'show'])
    ->name('reports.pdf');
