<?php

namespace Database\Factories;

use App\Models\Articles;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticlesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Articles::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(10),
            'cover' => $this->faker->imageUrl(150, 150),
            'full_text' => $this->faker->realText($minNbChars = 200),
            'likes_counter' => 0,
            'views_counter' => 0,
            'tags_id' => $this->faker->biasedNumberBetween(0, 10)
        ];
    }
}
