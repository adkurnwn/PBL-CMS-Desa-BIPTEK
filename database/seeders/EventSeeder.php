<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = ['Event 1', 'Event 2', 'Event 3'];
        foreach ($events as $event) {
            Event::create([
                'title' => $event,
                'user_id' => 1,
                'description' => 'Description for ' . $event,
                'date' => now()->addDays(rand(1, 30)),
                'time' => now()->addHours(rand(1, 24)),
                'location' => 'Location for ' . $event,
            ]);
        }
    }
}
