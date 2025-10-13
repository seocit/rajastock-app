<?php


use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');





Route::middleware(['auth'])->group(function () {

    
    Volt::route('items', 'superadmin.items.index')->name('items');
    Volt::route('merk', 'superadmin.merk.index')->name('merk');
    Volt::route('supplier','superadmin.supplier.index')->name('supplier');
    Volt::route('customer','superadmin.customer.index')->name('customer');
    Volt::route('purchases','superadmin.transaction.purchases')->name('purchases');
    Volt::route('create-purchases','superadmin.transaction.create-purchases')->name('create-purchases');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
