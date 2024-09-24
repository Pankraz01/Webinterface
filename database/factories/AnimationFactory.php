<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Animation>
 */
class AnimationFactory extends Factory
{
    protected $model = \App\Models\Animation::class;

    public function definition()
    {
        return [
            'file_name' => $this->faker->word() . '.png',
            'tags' => 'transition, ink, monochrom',
        ];
    }
}
