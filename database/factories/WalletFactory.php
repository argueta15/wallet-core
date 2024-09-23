<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Wallet;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'type' => $this->faker->randomElement(['income', 'expense']),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'amount' => $this->faker->numberBetween(100, 10000),
            'description' => $this->faker->sentence(5),
            'category_id' => Category::inRandomOrder()->firstOrFail()->id,
        ];
    }
}
