<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->unique()->numerify('TPB-#####'),
            'client_name' => $this->faker->name(),
            'client_number' => $this->faker->randomElement(['09', '07']) . $this->faker->numerify('########'),
            'device_type' => $this->faker->randomElement(['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other']),
            'device_model' => $this->faker->word() . ' ' . $this->faker->bothify('Model-##'),
            'serial_number' => $this->faker->optional()->bothify('SN-########'),
            'imei' => $this->faker->optional()->numerify('###############'),
            'issue_description' => $this->faker->optional()->sentence(8),
            'repair_status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled', 'delivered']),
        ];
    }
}
