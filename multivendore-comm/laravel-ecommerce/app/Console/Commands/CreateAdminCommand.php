<?php
namespace App\Console\Commands;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature   = 'admin:create';
    protected $description = 'Interactively create a new admin user';

    public function handle(): int
    {
        $email = $this->ask('Admin email');
        if (User::where('email', $email)->exists()) {
            $this->error("User {$email} already exists.");
            return self::FAILURE;
        }

        $password  = $this->secret('Password (min 8 chars)');
        $firstName = $this->ask('First name');
        $lastName  = $this->ask('Last name');

        $user = User::create([
            'email'            => $email,
            'password'         => Hash::make($password),
            'status'           => 'active',
            'email_verified_at'=> now(),
        ]);

        UserProfile::create(['user_id' => $user->id, 'first_name' => $firstName, 'last_name' => $lastName]);
        $user->assignRole('admin');

        $this->info("Admin {$email} created successfully.");
        return self::SUCCESS;
    }
}
