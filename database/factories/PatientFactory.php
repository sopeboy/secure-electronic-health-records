<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Synthetic patients only — never real people.
 *
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'first_name'    => fake()->firstName($gender),
            'last_name'     => fake()->lastName(),
            'gender'        => $gender,
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
            'phone'         => '080' . fake()->numerify('########'),
            'address'       => fake()->streetAddress() . ', Sokoto',
        ];
    }
}
