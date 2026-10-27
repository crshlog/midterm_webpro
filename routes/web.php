<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobBoardController;
use App\Http\Controllers\JobPostingController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Feature 1: Job Board Search & Filters
Route::get('/', [JobBoardController::class, 'landing'])->name('home');
Route::get('/jobs', [JobBoardController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobBoardController::class, 'show'])->name('jobs.show');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Feature 6: Job Bookmarking / Save List
    Route::post('/jobs/{job}/bookmark', [BookmarkController::class, 'toggle'])->name('jobs.bookmark');
    Route::get('/saved-jobs', [BookmarkController::class, 'index'])->name('bookmarks.index');

    // Freelancer Application Routes
    // Feature 3: Application Submission (Pitch, Portfolio URL, PDF CV upload)
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply');
    // Feature 5: Applicant Status Tracker
    Route::get('/my-applications', [JobApplicationController::class, 'myApplications'])->name('my-applications.index');
    // Feature 7: Application Withdrawal
    Route::delete('/my-applications/{application}/withdraw', [JobApplicationController::class, 'withdraw'])->name('my-applications.withdraw');

    // Employer Management Routes
    Route::prefix('employer')->name('employer.')->group(function () {
        // Feature 2: Job Posting CRUD & Toggle Active/Closed Status
        Route::patch('/jobs/{job}/toggle-status', [JobPostingController::class, 'toggleStatus'])->name('jobs.toggle-status');
        Route::resource('jobs', JobPostingController::class);

        // Feature 4: Employer Applicant Review Dashboard & Status Update
        Route::get('/applications', [JobApplicationController::class, 'employerReview'])->name('applications.index');
        Route::patch('/applications/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('applications.update-status');

        // Feature 8: Employer Company Profile CRUD (Bio, Logo Image, Social Media Links)
        Route::get('/profile', [EmployerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [EmployerProfileController::class, 'update'])->name('profile.update');
    });
});
