<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\SpecialOffer;
use App\Models\Review;
use App\Models\Request as ClientRequest;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\StatsController;

Route::get('/', [PageController::class, 'main'])->name('main');

Route::get('/price', [PageController::class, 'price'])->name('price');

Route::get('/services', [PageController::class, 'services'])->name('services');

Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/specialOffers', [PageController::class, 'specialOffers'])->name('specialOffers');

Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');

Route::get('/reviews', [PageController::class, 'reviews'])->name('reviews');
 
Route::get('/specialists', [PageController::class, 'specialists'])->name('specialists');

Route::get('/news', [PageController::class, 'news'])->name('news');

Route::get('/guarantees', [PageController::class, 'guarantees'])->name('guarantees');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    //Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/user/reviews', [App\Http\Controllers\User\ReviewController::class, 'store'])->name('user.reviews.store');
});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $statsController = new StatsController();
        return Inertia::render('Admin/Dashboard', [
            'stats' => $statsController->getStats(),
            'doctors' => \App\Models\Doctor::all(),
            'services' => \App\Models\Service::all(),
            'offers' => \App\Models\SpecialOffer::all(),
            'requests' => \App\Models\Request::all(),
            'reviews' => \App\Models\Review::all(),
            'prices' => \App\Models\Price::all(),
        ]);
    })->name('dashboard');
Route::get('/stats/export', [StatsController::class, 'export'])->name('stats.export');
});


Route::prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Doctor/Dashboard');
    })->name('dashboard');
});




require __DIR__.'/auth.php';



