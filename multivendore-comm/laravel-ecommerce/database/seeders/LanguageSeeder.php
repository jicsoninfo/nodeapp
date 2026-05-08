<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('languages')->truncate();

        DB::table('languages')->insert([
            ['code' => 'en', 'name' => 'English',    'native_name' => 'English',    'direction' => 'ltr', 'is_active' => 1, 'is_default' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'hi', 'name' => 'Hindi',      'native_name' => 'हिन्दी',     'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ar', 'name' => 'Arabic',     'native_name' => 'العربية',    'direction' => 'rtl', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'de', 'name' => 'German',     'native_name' => 'Deutsch',    'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'fr', 'name' => 'French',     'native_name' => 'Français',   'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'zh', 'name' => 'Chinese',    'native_name' => '中文',       'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ja', 'name' => 'Japanese',   'native_name' => '日本語',     'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'es', 'name' => 'Spanish',    'native_name' => 'Español',    'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português',  'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ru', 'name' => 'Russian',    'native_name' => 'Русский',    'direction' => 'ltr', 'is_active' => 1, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('  Languages seeded (10)');
    }
}
