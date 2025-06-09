<?php

namespace Database\Seeders;

use App\Models\AdditionalActivity;
use Illuminate\Database\Seeder;

class AdditionalActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Spa Treatment',
                'description' => 'Relaxing traditional spa treatment',
                'price' => 150.00
            ],
            [
                'name' => 'Scuba Diving',
                'description' => 'Discover underwater beauty',
                'price' => 250.00
            ],
            [
                'name' => 'Cooking Class',
                'description' => 'Learn local cuisine',
                'price' => 100.00
            ],
            [
                'name' => 'Photography Tour',
                'description' => 'Capture beautiful moments',
                'price' => 120.00
            ]
        ];

        foreach ($activities as $activity) {
            AdditionalActivity::create([
                'name' => $activity['name'],
                'description' => $activity['description'],
                'price' => $activity['price'],
                'is_active' => true
            ]);
        }
    }
}