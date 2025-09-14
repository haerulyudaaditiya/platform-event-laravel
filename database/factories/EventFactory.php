<?php
// database/factories/EventFactory.php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Buat user baru untuk event ini
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'start_time' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'end_time' => $this->faker->dateTimeBetween('+2 week', '+3 week'),
            'venue' => $this->faker->city(),
            'location' => $this->faker->address(),
            'is_published' => false, // Default-nya draft
        ];
    }
}
