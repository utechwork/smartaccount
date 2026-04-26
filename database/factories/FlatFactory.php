<?php

namespace Database\Factories;

use App\Models\Flat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flat>
 */
class FlatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Flat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flat_number' => 'FLT-' . $this->faker->numberBetween(100, 999),
            'owner_name' => $this->faker->name(),
            'owner_email' => $this->faker->email(),
            'owner_phone' => $this->faker->phoneNumber(),
            'floor_number' => $this->faker->numberBetween(1, 10),
            'total_area_sqft' => $this->faker->numberBetween(500, 2000),
        ];
    }
}
