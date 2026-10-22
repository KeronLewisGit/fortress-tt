<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Public site
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/services/{service:slug}', [PageController::class, 'service'])->name('services.show');
Route::get('/training', [PageController::class, 'training'])->name('training');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/track', [PageController::class, 'track'])->name('track');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');

// Careers & recruitment
Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/careers/apply', [CareerController::class, 'general'])->name('careers.general');
Route::post('/careers/apply', [CareerController::class, 'store'])->middleware('throttle:6,10')->name('careers.general.store');
Route::get('/careers/thank-you/{reference}', [CareerController::class, 'thanks'])->name('careers.thanks');
Route::get('/careers/{job}', [CareerController::class, 'show'])->name('careers.show');
Route::get('/careers/{job}/apply', [CareerController::class, 'apply'])->name('careers.apply');
Route::post('/careers/{job}/apply', [CareerController::class, 'store'])->middleware('throttle:6,10')->name('careers.apply.store');

// Staff authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin. Recruiters manage jobs, applicants, templates and enquiries; administrators also manage services, settings and users.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::post('jobs/{job}/duplicate', [Admin\JobController::class, 'duplicate'])->name('jobs.duplicate');
    Route::patch('jobs/{job}/status', [Admin\JobController::class, 'status'])->name('jobs.status');
    Route::resource('jobs', Admin\JobController::class)->except(['show']);

    Route::get('applications/pipeline', [Admin\ApplicationController::class, 'pipeline'])->name('applications.pipeline');
    Route::get('applications/export', [Admin\ApplicationController::class, 'export'])->name('applications.export');
    Route::post('applications/{application}/notes', [Admin\ApplicationController::class, 'note'])->name('applications.notes.store');
    Route::post('applications/{application}/message', [Admin\ApplicationController::class, 'message'])->name('applications.message');
    Route::get('applications/{application}/resume', [Admin\ApplicationController::class, 'resume'])->name('applications.resume');
    Route::resource('applications', Admin\ApplicationController::class)->only(['index', 'show', 'update']);
    Route::delete('applications/{application}', [Admin\ApplicationController::class, 'destroy'])->middleware('admin')->name('applications.destroy');

    Route::resource('enquiries', Admin\EnquiryController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('templates', Admin\TemplateController::class)->except(['show']);

    Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('admin')->group(function () {
        Route::resource('services', Admin\ServiceController::class)->except(['show']);
        Route::get('settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::resource('users', Admin\UserController::class)->except(['show']);
    });
});
