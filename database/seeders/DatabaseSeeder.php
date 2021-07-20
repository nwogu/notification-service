<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (['topic1', 'topic2'] as $topic) {
            Topic::firstOrCreate([
                'name' => $topic
            ]);
        }
    }
}
