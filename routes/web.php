<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Portal;
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

// Applicant portal
Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest:applicant')->group(function () {
        Route::get('register', [Portal\AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [Portal\AuthController::class, 'register'])->middleware('throttle:6,10');
        Route::get('login', [Portal\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Portal\AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::get('forgot-password', [Portal\AuthController::class, 'showForgot'])->name('password.request');
        Route::post('forgot-password', [Portal\AuthController::class, 'sendReset'])->middleware('throttle:5,10')->name('password.email');
        Route::get('reset-password/{token}', [Portal\AuthController::class, 'showReset'])->name('password.reset');
        Route::post('reset-password', [Portal\AuthController::class, 'reset'])->middleware('throttle:5,10')->name('password.update');
    });
    Route::middleware('auth:applicant')->group(function () {
        Route::get('/', [Portal\PortalController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [Portal\AuthController::class, 'logout'])->name('logout');
        Route::get('applications/{reference}', [Portal\PortalController::class, 'show'])->name('applications.show');
        Route::post('applications/{reference}/book', [Portal\PortalController::class, 'book'])->middleware('throttle:10,1')->name('applications.book');
        Route::get('notifications', [Portal\PortalController::class, 'notifications'])->name('notifications');
        Route::get('profile', [Portal\PortalController::class, 'profile'])->name('profile');
        Route::put('profile', [Portal\PortalController::class, 'updateProfile'])->name('profile.update');
        Route::post('resume', [Portal\PortalController::class, 'storeResume'])->name('resume.store');
        Route::get('resume', [Portal\PortalController::class, 'downloadResume'])->name('resume.download');
        Route::delete('resume', [Portal\PortalController::class, 'destroyResume'])->name('resume.destroy');
    });
});

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

    Route::get('interviews', [Admin\InterviewController::class, 'index'])->name('interviews.index');
    Route::post('interviews', [Admin\InterviewController::class, 'store'])->name('interviews.store');
    Route::delete('interviews/{slot}', [Admin\InterviewController::class, 'destroy'])->name('interviews.destroy');

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
