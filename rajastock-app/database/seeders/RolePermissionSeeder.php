<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('permission:cache-reset');

        // ======================================================
        // PERMISSIONS LIST
        // ======================================================
        $permissions = [

            // Master data
            'view items', 'create items', 'edit items', 'delete items',
            'view brands', 'create brands', 'edit brands', 'delete brands',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view customers', 'create customers', 'edit customers', 'delete customers',

            // Stok
            'view purchases', 'create purchases', 'edit purchases', 'delete purchases',
            'view sales', 'create sales', 'edit sales', 'delete sales',
            'view purchase returns', 'create purchase returns', 'edit purchase returns', 'delete purchase returns',
            'view sales returns', 'create sales returns', 'edit sales returns', 'delete sales returns',

            // Users
            'view users', 'create users', 'edit users', 'delete users',

            // Roles
            'view roles', 'create roles', 'edit roles', 'delete roles',

            // Reports
            'view reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ======================================================
        // ROLES
        // ======================================================
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $kasir      = Role::firstOrCreate(['name' => 'kasir']);

        // SUPERADMIN = ALL PERMISSIONS
        $superadmin->syncPermissions(Permission::all());

        // ADMIN = hampir semua, kecuali manajemen roles
        $admin->syncPermissions([
            // Master data
            'view items', 'create items', 'edit items', 'delete items',
            'view brands', 'create brands', 'edit brands', 'delete brands',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view customers', 'create customers', 'edit customers', 'delete customers',

            // Stok
            'view purchases', 'create purchases', 'edit purchases', 'delete purchases',
            'view sales', 'create sales', 'edit sales', 'delete sales',
            'view purchase returns', 'create purchase returns', 'edit purchase returns', 'delete purchase returns',
            'view sales returns', 'create sales returns', 'edit sales returns', 'delete sales returns',

            // Users
            'view users', 'create users', 'edit users',

            // Reports
            'view reports',
        ]);

        // KASIR = transaksi + lihat barang
        $kasir->syncPermissions([
            'view items',
            'view suppliers',
            'view customers',
            'view sales', 'create sales',
            'view sales returns', 'create sales returns',
            'view customers',
        ]);

        // OPTIONAL: Assign superadmin ke user pertama
        if (\App\Models\User::count() > 0) {
            \App\Models\User::first()->assignRole('superadmin');
        }
    }
}
