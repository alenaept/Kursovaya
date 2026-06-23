<?php

use App\Http\Controllers\Public\DoctorController as PublicDoctorController;
use App\Http\Controllers\Public\ServiceController as PublicServiceController;
use App\Http\Controllers\Public\SpecialOfferController as PublicSpecialOfferController;
use App\Http\Controllers\Public\ReviewController as PublicReviewController;
use App\Http\Controllers\Public\RequestController as PublicRequestController;
use App\Http\Controllers\Public\PriceController as PublicPriceController;

use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SpecialOfferController as AdminSpecialOfferController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\PriceController as AdminPriceController;

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Doctor\DoctorController as DoctorController;

// ========== ПУБЛИЧНЫЕ МАРШРУТЫ (без защиты) ==========
Route::prefix('public')->group(function () {
    Route::get('/doctors', [PublicDoctorController::class, 'index']);
    Route::get('/doctors/{id}', [PublicDoctorController::class, 'show']);
    Route::get('/services', [PublicServiceController::class, 'index']);
    Route::get('/services/{id}', [PublicServiceController::class, 'show']);
    Route::get('/special-offers', [PublicSpecialOfferController::class, 'index']);
    Route::get('/special-offers/{id}', [PublicSpecialOfferController::class, 'show']);
    Route::get('/reviews', [PublicReviewController::class, 'index']);
    Route::get('/prices', [PublicPriceController::class, 'index']);
    Route::get('/prices/grouped', [PublicPriceController::class, 'getGroupedPrices']);
    Route::post('/requests', [PublicRequestController::class, 'store']);
});


Route::get('/slots/{doctorId}', [AppointmentController::class, 'getDoctorAvailableSlots']);

// ЗАЩИЩЁННЫЕ МАРШРУТЫ 
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Запись на приём
    Route::post('/book-slot', [AppointmentController::class, 'bookSlot']);
    Route::get('/my-appointments', [AppointmentController::class, 'getUserAppointments']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'cancelAppointment']);
    
    // Админ маршруты
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('/doctors', [AdminDoctorController::class, 'index']);
        Route::post('/doctors', [AdminDoctorController::class, 'store']);
        Route::put('/doctors/{id}', [AdminDoctorController::class, 'update']);
        Route::delete('/doctors/{id}', [AdminDoctorController::class, 'destroy']);
        
        Route::get('/services', [AdminServiceController::class, 'index']);
        Route::post('/services', [AdminServiceController::class, 'store']);
        Route::put('/services/{id}', [AdminServiceController::class, 'update']);
        Route::delete('/services/{id}', [AdminServiceController::class, 'destroy']);
        
        Route::get('/special-offers', [AdminSpecialOfferController::class, 'index']);
        Route::post('/special-offers', [AdminSpecialOfferController::class, 'store']);
        Route::put('/special-offers/{id}', [AdminSpecialOfferController::class, 'update']);
        Route::delete('/special-offers/{id}', [AdminSpecialOfferController::class, 'destroy']);
        
        Route::get('/reviews', [AdminReviewController::class, 'index']);
        Route::post('/reviews/{id}/publish', [AdminReviewController::class, 'publish']);
        Route::delete('/reviews/{id}/unpublish', [AdminReviewController::class, 'unpublish']);
        Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);
        
        Route::get('/requests', [AdminRequestController::class, 'index']);
        Route::delete('/requests/{id}', [AdminRequestController::class, 'destroy']);
        
        Route::get('/prices', [AdminPriceController::class, 'index']);
        Route::post('/prices', [AdminPriceController::class, 'store']);
        Route::put('/prices/{id}', [AdminPriceController::class, 'update']);
        Route::delete('/prices/{id}', [AdminPriceController::class, 'destroy']);
    });
    
    // Доктор маршруты
    Route::prefix('doctor')->middleware(['doctor'])->group(function () {
        Route::get('/appointments/{doctorId}', [DoctorController::class, 'getAppointments']);
        Route::get('/schedule/{doctorId}', [DoctorController::class, 'getSchedule']);
        Route::post('/slots', [DoctorController::class, 'addSlot']);
        Route::put('/slots/{id}/toggle', [DoctorController::class, 'toggleSlot']);
        Route::delete('/slots/{id}', [DoctorController::class, 'deleteSlot']);
    });
});