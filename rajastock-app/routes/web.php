<?php

use App\Livewire\Dashboard\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');






Route::middleware(['auth'])->group(function () {
    
    Route::get('dashboard', Dashboard::class)
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Volt::route('items', 'superadmin.items.index')->name('items');
    Volt::route('merk', 'superadmin.merk.index')->name('merk');
    Volt::route('supplier', 'superadmin.supplier.index')->name('supplier');
    Volt::route('customer', 'superadmin.customer.index')->name('customer');


    // transaction

    // purcahses
    Volt::route('purchases', 'superadmin.transaction.purchases')->name('purchases');
    Volt::route('create-purchases', 'superadmin.transaction.create-purchases')->name('create-purchases');
    Volt::route('edit-purchases/{id}', 'superadmin.transaction.edit-purchases')->name('edit-purchases');
    
    // sales
    Volt::route('sales', 'superadmin.transaction.sales')->name('sales');
    Volt::route('create-sale', 'superadmin.transaction.create-sale')->name('create-sale');
    Volt::route('edit-sale/{id}', 'superadmin.transaction.edit-sale')->name('edit-sale');
    
    // end transaction

    // Purchase Return
    Volt::route('purchase-returns', 'superadmin.return.index-purchase-return')->name('purchase-returns');
    Volt::route('purchase-returns/create-return', 'superadmin.return.create-purchase-return')->name('create-purchase-returns');
    Volt::route('edit-returns/{id}', 'superadmin.return.edit-purchase-return')->name('edit-purchase');
    
    // Sale Return
    Volt::route('sale-returns', 'superadmin.return.index-sale-returns')->name('sale-returns');
    Volt::route('sale-returns/create-return', 'superadmin.return.create-sale-returns')->name('create-sale-returns');
    Volt::route('sale-returns/{id}', 'superadmin.return.edit-sale-return')->name('edit-sale-returns');


    // User Management
    Volt::route('users', 'superadmin.users.users')->name('users');
    Volt::route('roles', 'superadmin.users.roles')->name('roles');

    // Audit Logs
    Volt::route('audit-logs', 'superadmin.audit.audit-logs')->name('audit-logs');

    // Settings
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__ . '/auth.php';
