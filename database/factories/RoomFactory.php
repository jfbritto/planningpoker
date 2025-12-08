<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'code' => Room::generateCode(),
            'name' => $this->faker->words(3, true),
            'is_active' => true,
        ];
    }
}

