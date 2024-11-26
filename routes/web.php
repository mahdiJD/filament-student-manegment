<?php

use App\Http\Controllers\invoicesController;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('{student}/invo/generate',[invoicesController::class, 'generatePdf'])->name('student.invo.generat');

