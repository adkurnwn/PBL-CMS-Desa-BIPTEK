<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Helpers\SlugHelper;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                'slug' => SlugHelper::generateUniqueSlug(Event::class, $event),
                'user_id' => 1,
                'description' => 'Description for ' . $event,
                'date' => now()->addDays(rand(1, 30)),
                'time' => now()->addHours(rand(1, 24)),
                'location' => 'Location for ' . $event,
            ]);
        }
    }
}
