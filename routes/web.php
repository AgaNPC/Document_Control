<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PrintRequestController;
use App\Http\Controllers\LifecycleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/switch/{id}', [AuthController::class, 'loginAs'])->name('login.switch');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('repository.index');
    });

    // Page 1: Perpustakaan Dokumen
    Route::get('/repository', [DocumentController::class, 'index'])->name('repository.index');
    Route::get('/documents/{id}/stream', [DocumentController::class, 'stream'])->name('documents.stream');

    // Page 2: Penggandaan Dokumen (Controlled Copy & Print)
    Route::get('/print-requests', [PrintRequestController::class, 'index'])->name('print.index');
    Route::post('/print-requests', [PrintRequestController::class, 'store'])->name('print.store');
    Route::get('/print-requests/{id}/download', [PrintRequestController::class, 'download'])->name('print.download');

    // Page 3: Pengendalian Dokumen (Lifecycle Control)
    Route::get('/lifecycle', [LifecycleController::class, 'index'])->name('lifecycle.index');
    Route::post('/lifecycle/registration', [LifecycleController::class, 'storeRegistration'])->name('lifecycle.registration');
    Route::post('/lifecycle/revision', [LifecycleController::class, 'storeRevision'])->name('lifecycle.revision');
    Route::post('/lifecycle/obsolete', [LifecycleController::class, 'storeObsolete'])->name('lifecycle.obsolete');

    // API / In-App Notifications & Approval Actions
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.count');
    Route::get('/api/notifications', [NotificationController::class, 'list'])->name('api.notifications.list');
    Route::post('/api/notifications/{id}/approve', [NotificationController::class, 'approve'])->name('api.notifications.approve');
    Route::post('/api/notifications/{id}/reject', [NotificationController::class, 'reject'])->name('api.notifications.reject');
});
