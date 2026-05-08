<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchQuerySeeder extends Seeder
{
    public function run(): void
    {
        $queries = [
            ['iphone 15 pro max', 'en'], ['samsung galaxy s24 ultra', 'en'],
            ['laptop 4k oled', 'en'], ['wireless earbuds', 'en'],
            ['nike air max 270', 'en'], ['adidas ultraboost women', 'en'],
            ['atomic habits book', 'en'], ['sapiens book', 'en'],
            ['سماعات سوني', 'ar'], ['كتب تطوير الذات', 'ar'],
            ['स्मार्टफोन 2024', 'hi'], ['laptop günstig', 'de'],
            ['chaussures de course', 'fr'], ['智能手机推荐', 'zh'],
        ];

        $users = User::role('buyer')->take(8)->pluck('id')->toArray();

        foreach ($queries as [$query, $lang]) {
            DB::table('search_queries')->insert([
                'id'            => Str::uuid(),
                'user_id'       => $users[array_rand($users)],
                'session_id'    => null,
                'query'         => $query,
                'lang_code'     => $lang,
                'results_count' => rand(5, 80),
                'searched_at'   => now()->subDays(rand(0, 30)),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info('  Search queries seeded (' . count($queries) . ')');
    }
}
