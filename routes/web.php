<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Filament\Resources\QuoteResource;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});



//Filament::registerResources([
//    QuoteResource::class,
//]);

require __DIR__.'/auth.php';
