<?php
namespace Tests\Feature\Vendor;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_apply_as_vendor(): void
    {
        $user = $this->actingAsUser('buyer');
        $user->update(['email_verified_at' => now()]);

        $this->postJson('/api/v1/onboarding/vendor/apply', [
            'store_name'    => 'My Test Store',
            'business_type' => 'individual',
            'description'   => 'This is my test store description.',
            'plan_type'     => 'free',
            'agree_terms'   => true,
        ])->assertStatus(201)->assertJsonPath('data.store_name', 'My Test Store');

        $this->assertDatabaseHas('vendors', ['owner_user_id' => $user->id, 'status' => 'pending']);
    }

    public function test_admin_can_approve_vendor(): void
    {
        $this->actingAsAdmin();
        $vendor = Vendor::factory()->create(['status' => 'pending']);
        VendorProfile::factory()->create(['vendor_id' => $vendor->id]);

        $this->postJson("/api/v1/admin/vendors/{$vendor->id}/approve")
             ->assertStatus(200);

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'status' => 'active']);
    }

    public function test_admin_can_suspend_vendor(): void
    {
        $this->actingAsAdmin();
        $vendor = Vendor::factory()->create(['status' => 'active']);
        VendorProfile::factory()->create(['vendor_id' => $vendor->id]);

        $this->postJson("/api/v1/admin/vendors/{$vendor->id}/suspend", ['reason' => 'Policy violation'])
             ->assertStatus(200);

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'status' => 'suspended']);
    }

    public function test_inactive_vendor_cannot_access_vendor_routes(): void
    {
        $user   = $this->actingAsUser('vendor');
        $vendor = Vendor::factory()->create(['owner_user_id' => $user->id, 'status' => 'pending']);
        VendorProfile::factory()->create(['vendor_id' => $vendor->id]);

        $this->getJson('/api/v1/vendor/dashboard')->assertStatus(403);
    }

    public function test_vendor_dashboard_returns_stats(): void
    {
        [$user, $vendor] = $this->actingAsVendor();

        $this->getJson('/api/v1/vendor/dashboard')
             ->assertStatus(200)
             ->assertJsonStructure(['data' => ['stats']]);
    }
}
