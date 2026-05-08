<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ───────────────────────────────────
        $permissions = [
            // Products
            'products.view', 'products.create', 'products.update', 'products.delete', 'products.publish',
            // Orders
            'orders.view', 'orders.update', 'orders.cancel', 'orders.refund', 'orders.export',
            // Vendors
            'vendors.view', 'vendors.approve', 'vendors.suspend', 'vendors.manage-commission',
            // Users
            'users.view', 'users.manage', 'users.impersonate',
            // Reviews
            'reviews.view', 'reviews.moderate',
            // Payouts
            'payouts.view', 'payouts.process',
            // Analytics
            'analytics.view', 'analytics.export',
            // Settings
            'settings.view', 'settings.manage',
            // Categories & Brands
            'categories.manage', 'brands.manage', 'attributes.manage',
            // Coupons
            'coupons.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // ── Roles ─────────────────────────────────────────

        // Admin — all permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->syncPermissions(Permission::all());

        // Vendor — own product & order management
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'sanctum']);
        $vendor->syncPermissions([
            'products.view', 'products.create', 'products.update', 'products.delete',
            'orders.view', 'orders.update', 'orders.export',
            'reviews.view',
            'payouts.view',
            'analytics.view', 'analytics.export',
            'coupons.manage',
        ]);

        // Buyer — basic shopping
        $buyer = Role::firstOrCreate(['name' => 'buyer', 'guard_name' => 'sanctum']);
        $buyer->syncPermissions([
            'products.view',
            'orders.view', 'orders.cancel',
            'reviews.view',
        ]);

        // Support — read + cancel/refund
        $support = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'sanctum']);
        $support->syncPermissions([
            'products.view',
            'orders.view', 'orders.cancel', 'orders.refund',
            'users.view',
            'reviews.view', 'reviews.moderate',
            'vendors.view',
        ]);

        // Moderator — content review
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'sanctum']);
        $moderator->syncPermissions([
            'products.view', 'products.publish',
            'reviews.view', 'reviews.moderate',
        ]);

        $this->command->info('  Roles & permissions seeded (5 roles, ' . count($permissions) . ' permissions)');
    }
}
