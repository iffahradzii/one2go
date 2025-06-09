<?php

namespace Database\Seeders;

use App\Models\TravelPackage;
use Illuminate\Database\Seeder;

class TravelPackageSeeder extends Seeder
{
    public function run(): void
    {
        $countries = ['Indonesia', 'Thailand', 'Vietnam', 'South Korea'];
        
        foreach ($countries as $country) {
            TravelPackage::create([
                'name' => "{$country} Adventure Tour",
                'country' => $country,
                'price' => rand(1500, 5000),
                'description' => "Experience the beauty of {$country} with our comprehensive tour package.",
                'itinerary' => [
                    'Day 1' => 'Arrival and Welcome Dinner',
                    'Day 2' => 'City Tour and Cultural Experience',
                    'Day 3' => 'Nature and Adventure Activities',
                    'Day 4' => 'Free and Easy Day',
                    'Day 5' => 'Departure'
                ],
                'include' => [
                    'Hotel Accommodation',
                    'Breakfast',
                    'Tour Guide',
                    'Transportation',
                    'Entrance Fees'
                ],
                'exclude' => [
                    'Flight Tickets',
                    'Travel Insurance',
                    'Personal Expenses',
                    'Optional Tours'
                ],
                'available_dates' => [
                    now()->addDays(30)->format('Y-m-d'),
                    now()->addDays(60)->format('Y-m-d'),
                    now()->addDays(90)->format('Y-m-d')
                ],
                'is_visible' => true
            ]);
        }
    }
}