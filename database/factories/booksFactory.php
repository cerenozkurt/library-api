<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Books;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class booksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->streetName(),
            'isbn' => $this->faker->unique()->numberBetween(1000000000000, 9999999999999),
            'page_count'=>$this->faker->unique(true)->numberBetween(1, 2000), 
            'publisher_id'=> Publisher::all()->random()->id,
            'category_id'=>Category::all()->random()->id,
            'author_id'=>Author::all()->random()->id,
            'read_count' => 0
        ];
    }
}
