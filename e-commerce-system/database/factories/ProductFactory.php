<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'image_url' => $this->faker->url(),
            'price' => $this->faker->randomFloat(3),
            'expiry_date' => $this->faker->date('Y-m-d H:i:s'),
            'phone_number' => $this->faker->phoneNumber(),
            'description' => $this->faker->text(),
            'quantity' => rand(1, 100),
            'user_id' => rand(1, 100),
            'category_id' => rand(1,7),
        ];
    }
}
