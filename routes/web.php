<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomBrowseController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('blade_full_page')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('home');
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');
});

Route::middleware(['auth', 'verified', 'blade_full_page'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/rooms/{room}/favorite', [RoomBrowseController::class, 'toggleFavorite'])
        ->name('rooms.favorite.toggle');
    Route::get('/rooms/{room}/book', [RoomBrowseController::class, 'quickBook'])->name('rooms.quickBook');
    Route::get('/favorite-rooms', [RoomBrowseController::class, 'favorites'])->name('favorite-rooms.index');

    Route::get('/my-reservations', [ReservationController::class, 'my'])->name('reservations.my');
    Route::get('/calendar', static function (Request $request): RedirectResponse {
        return redirect()->route('reservations.my', array_merge(
            ['view' => 'calendar'],
            array_filter(
                $request->only(['year', 'month', 'room_id', 'date']),
                fn ($value) => $value !== null && $value !== '',
            ),
        ));
    })->name('reservations.calendar');
    Route::get('/reservations/room-booked-slots', [ReservationController::class, 'roomBookedSlots'])->name('reservations.roomBookedSlots');
    Route::get('/reservations/{id}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::put('/reservations/{id}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

Route::middleware(['auth', 'admin', 'blade_full_page'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/rooms', [RoomController::class, 'adminIndex'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{id}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::get('/rooms/{id}', [RoomController::class, 'adminShow'])->name('rooms.show');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::patch('/rooms/{id}/restore', [RoomController::class, 'restore'])->name('rooms.restore');

    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{id}/edit', [ReservationController::class, 'adminEdit'])->name('reservations.edit');
    Route::put('/reservations/{id}', [ReservationController::class, 'adminUpdate'])->name('reservations.update');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');

});

Route::middleware(['auth', 'super_admin', 'blade_full_page'])->prefix('admin')->name('admin.')->group(function () {
    Route::put('/users/{id}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
});

require __DIR__.'/settings.php';
