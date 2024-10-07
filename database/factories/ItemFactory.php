<?php
// database/factories/ItemFactory.php
namespace Database\Factories;

use App\Models\Item;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'category_id' => ProductCategory::factory(),
        ];
    }
}
