<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntervalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('intervals')->upsert([
            [
                'name' => 'day',
                'slug' => 'day',
                'date_identifier' => 'day',
                'adverb' => 'daily',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'week',
                'slug' => 'week',
                'date_identifier' => 'week',
                'adverb' => 'weekly',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'month',
                'slug' => 'month',
                'date_identifier' => 'month',
                'adverb' => 'monthly',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'year',
                'slug' => 'year',
                'date_identifier' => 'year',
                'adverb' => 'yearly',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ], ['slug'], ['name', 'date_identifier']);
    }
}
