<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home redirects to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Auth Routes
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Admin Routes (protected)
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Form Routes
    Route::get('/forms', [FormController::class, 'index'])->name('admin.forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('admin.forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('admin.forms.store');
    Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('admin.forms.edit');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('admin.forms.update');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('admin.forms.destroy');
    Route::post('/forms/{form}/toggle-status', [FormController::class, 'toggleStatus'])->name('admin.forms.toggle');
    Route::get('/forms/{form}/submissions', [FormController::class, 'submissions'])->name('admin.forms.submissions');
    
    // Question Routes
    Route::post('/forms/{form}/questions', [QuestionController::class, 'store'])->name('admin.questions.store');
    Route::get('/forms/{form}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('admin.questions.edit');
    Route::put('/forms/{form}/questions/{question}', [QuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('/forms/{form}/questions/{question}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');
    Route::post('/forms/{form}/questions/reorder', [QuestionController::class, 'reorder'])->name('admin.questions.reorder');

    // Export Route
    Route::get('/forms/{form}/export', [FormController::class, 'export'])->name('admin.forms.export');
});

// Public Form Routes
Route::get('/form/{slug}', [PublicFormController::class, 'show'])->name('public.show');
Route::post('/form/{slug}/register', [PublicFormController::class, 'register'])->name('public.register');
Route::get('/form/{slug}/question/{index}', [PublicFormController::class, 'question'])->name('public.question');
Route::post('/form/{slug}/answer', [PublicFormController::class, 'submitAnswer'])->name('public.answer');
Route::get('/form/{slug}/result', [PublicFormController::class, 'result'])->name('public.result');
Route::get('/form/{slug}/certificate/download', [PublicFormController::class, 'downloadCertificate'])->name('public.certificate.download');
