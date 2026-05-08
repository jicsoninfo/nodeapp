<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /** Create and authenticate a user with optional role. */
    protected function actingAsUser(string $role = 'buyer', array $attributes = []): \App\Models\User
    {
        $user = \App\Models\User::factory()->create(array_merge([
            'status'            => 'active',
            'email_verified_at' => now(),
        ], $attributes));
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    /** Create and authenticate an admin. */
    protected function actingAsAdmin(): \App\Models\User
    {
        return $this->actingAsUser('admin');
    }

    /** Create and authenticate a vendor with active vendor store. */
    protected function actingAsVendor(): array
    {
        $user   = $this->actingAsUser('vendor');
        $vendor = \App\Models\Vendor::factory()->create([
            'owner_user_id' => $user->id,
            'status'        => 'active',
        ]);
        \App\Models\VendorProfile::factory()->create(['vendor_id' => $vendor->id]);
        return [$user, $vendor];
    }
}
