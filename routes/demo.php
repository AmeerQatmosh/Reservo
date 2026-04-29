<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'demo.enabled', 'throttle:demo'])
    ->prefix('demo')
    ->name('demo.')
    ->group(function () {
        Route::get('/', [DemoController::class, 'index'])->name('index');
        Route::post('/start', [DemoController::class, 'start'])->name('start');
        Route::get('/exit', [DemoController::class, 'exitDemo'])->name('exit');

        Route::middleware('demo.session')->group(function () {
            Route::get('/hub', [DemoController::class, 'hub'])->name('hub');
            Route::post('/role', [DemoController::class, 'switchRole'])->name('role');

            Route::get('/rooms', [DemoController::class, 'rooms'])->name('rooms');
            Route::post('/rooms/{id}/favorite', [DemoController::class, 'toggleRoomFavorite'])->name('rooms.favorite.toggle');
            Route::get('/rooms/{id}/book', [DemoController::class, 'quickBookRoom'])->name('rooms.quickBook');
            Route::get('/rooms/{id}', [DemoController::class, 'roomShow'])->name('room.show');
            Route::get('/calendar', [DemoController::class, 'calendar'])->name('calendar');
            Route::get('/reservations/my', [DemoController::class, 'reservationsMy'])->name('reservations.my');
            Route::get('/reservations/room-booked-slots', [DemoController::class, 'roomBookedSlots'])->name('reservations.roomBookedSlots');
            Route::post('/reservations', [DemoController::class, 'storeReservation'])->name('reservations.store');
            Route::delete('/reservations/{id}', [DemoController::class, 'destroyReservation'])->name('reservations.destroy');

            Route::get('/admin/rooms/create', [DemoController::class, 'createRoom'])->name('admin.rooms.create');
            Route::post('/admin/rooms', [DemoController::class, 'storeRoom'])->name('admin.rooms.store');
            Route::get('/admin/rooms', [DemoController::class, 'adminRooms'])->name('admin.rooms');
            Route::get('/admin/rooms/{id}', [DemoController::class, 'adminRoomShow'])->name('admin.rooms.show');
            Route::delete('/admin/rooms/{id}', [DemoController::class, 'destroyRoom'])->name('admin.rooms.destroy');

            Route::get('/admin/reservations', [DemoController::class, 'adminReservations'])->name('admin.reservations');
            Route::get('/admin/users', [DemoController::class, 'adminUsers'])->name('admin.users');
        });
    });
