<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Image;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'mini_description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 1, 1000),
            'stock' => fake()->numberBetween(0, 100),
            'limit_threshold' => fake()->numberBetween(1, 10),
            'out_of_stock_threshold' => fake()->numberBetween(0, 5),
            'status' => fake()->randomElement(['disponible', 'limite', 'rupture']),
            'slug' => fake()->slug(),
            'search_slug' => fake()->slug(),
            'search_slug_metaphone' => fake()->optional()->word(),
            'promotion_pourcentage' => fake()->optional()->randomFloat(2, 0, 50),
            'promotion_discount' => fake()->optional()->randomFloat(2, 0, 100),
        ];
    }

    public function withImages()
    {
        return $this->afterCreating(function($item) {
            $images = Image::factory()->count(rand(1, 2))->create(['item_ref' => $item->ref]);
            
            foreach ($images as $image) {
                // Télécharger une image à partir d'une URL
                $imageUrl = 'https://picsum.photos/680/480';

                $imageContents = Http::get($imageUrl)->body();
                $imageName = Str::random(10) . '.jpg';
                $imagePath = "images/{$image->item_ref}/" . $imageName;

                // Stocker l'image dans le storage
                Storage::disk('public')->put($imagePath, $imageContents);
                $image->update(['path' => $imagePath]);
            }
        });
    }

}
