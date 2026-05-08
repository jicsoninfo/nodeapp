<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────
        $admin = User::factory()->create([
            'email'            => 'admin@marketplace.com',
            'password'         => Hash::make('Admin@123456'),
            'status'           => 'active',
            'email_verified_at'=> now(),
        ]);
        UserProfile::factory()->create(['user_id' => $admin->id, 'first_name' => 'Platform', 'last_name' => 'Admin', 'locale' => 'en']);
        $admin->assignRole('admin');

        // ── Support ───────────────────────────────────────
        $support = User::factory()->create([
            'email'            => 'support@marketplace.com',
            'status'           => 'active',
            'email_verified_at'=> now(),
        ]);
        UserProfile::factory()->create(['user_id' => $support->id, 'first_name' => 'Support', 'last_name' => 'Agent', 'locale' => 'en']);
        $support->assignRole('support');

        // ── Vendor users ──────────────────────────────────
        $vendorData = [
            ['email' => 'rahul.sharma@techstore.in',   'first_name' => 'Rahul',   'last_name' => 'Sharma',   'locale' => 'hi', 'phone' => '+919876543210'],
            ['email' => 'emma.johnson@fashionhub.com', 'first_name' => 'Emma',    'last_name' => 'Johnson',  'locale' => 'en', 'phone' => '+14155550199'],
            ['email' => 'ahmed.ali@bookworld.ae',      'first_name' => 'Ahmed',   'last_name' => 'Ali',      'locale' => 'ar', 'phone' => '+971501234567'],
            ['email' => 'li.wei@gadgetzone.cn',        'first_name' => 'Li',      'last_name' => 'Wei',      'locale' => 'zh', 'phone' => '+8613800138000'],
        ];

        foreach ($vendorData as $data) {
            $user = User::factory()->create([
                'email'            => $data['email'],
                'phone'            => $data['phone'],
                'status'           => 'active',
                'email_verified_at'=> now(),
            ]);
            UserProfile::factory()->create([
                'user_id'    => $user->id,
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'locale'     => $data['locale'],
            ]);
            $user->assignRole('vendor');
            $user->assignRole('buyer');
        }

        // ── Buyers ────────────────────────────────────────
        $buyerData = [
            ['email' => 'alice.mueller@gmail.com',    'first_name' => 'Alice',    'last_name' => 'Müller',    'locale' => 'de', 'phone' => '+4917612345678'],
            ['email' => 'mohammed.hassan@outlook.com','first_name' => 'Mohammed', 'last_name' => 'Al-Hassan', 'locale' => 'ar', 'phone' => '+971521234567'],
            ['email' => 'priya.verma@yahoo.com',      'first_name' => 'Priya',    'last_name' => 'Verma',     'locale' => 'hi', 'phone' => '+919988776655'],
            ['email' => 'john.smith@icloud.com',      'first_name' => 'John',     'last_name' => 'Smith',     'locale' => 'en', 'phone' => '+16505551234'],
            ['email' => 'yuki.tanaka@docomo.jp',      'first_name' => 'Yuki',     'last_name' => 'Tanaka',    'locale' => 'ja', 'phone' => '+819012345678'],
            ['email' => 'carlos.gomez@hotmail.es',    'first_name' => 'Carlos',   'last_name' => 'Gómez',     'locale' => 'es', 'phone' => '+34612345678'],
            ['email' => 'fatima.al-zahra@gmail.com',  'first_name' => 'Fatima',   'last_name' => 'Al-Zahra',  'locale' => 'ar', 'phone' => '+966551234567'],
            ['email' => 'pierre.dupont@laposte.fr',   'first_name' => 'Pierre',   'last_name' => 'Dupont',    'locale' => 'fr', 'phone' => '+33612345678'],
        ];

        foreach ($buyerData as $data) {
            $user = User::factory()->create([
                'email'            => $data['email'],
                'phone'            => $data['phone'],
                'status'           => 'active',
                'email_verified_at'=> now(),
            ]);
            UserProfile::factory()->create([
                'user_id'    => $user->id,
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'locale'     => $data['locale'],
            ]);
            $user->assignRole('buyer');
        }

        // ── Extra random buyers (for realistic data volume) ─
        User::factory(50)
            ->has(UserProfile::factory(), 'profile')
            ->create()
            ->each(fn ($u) => $u->assignRole('buyer'));

        $this->command->info('  Users seeded (2 admin/support + 4 vendors + 8 named buyers + 50 random buyers)');
    }
}
