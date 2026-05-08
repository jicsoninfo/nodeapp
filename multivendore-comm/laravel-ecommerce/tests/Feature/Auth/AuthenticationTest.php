<?php
namespace Tests\Feature\Auth;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'John',
            'last_name'             => 'Doe',
            'email'                 => 'john@example.com',
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id','email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Jane',
            'last_name'             => 'Doe',
            'email'                 => 'john@example.com',
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email'             => 'login@example.com',
            'password'          => bcrypt('Password@123'),
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('buyer');

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'login@example.com',
            'password' => 'Password@123',
        ])->assertStatus(200)->assertJsonStructure(['data','token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('Correct@123')]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'WrongPassword',
        ])->assertStatus(422);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'suspended@example.com',
            'password' => bcrypt('Password@123'),
            'status'   => 'suspended',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'suspended@example.com',
            'password' => 'Password@123',
        ])->assertStatus(403);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAsUser();
        $this->postJson('/api/v1/auth/logout')->assertStatus(200);
    }
}
