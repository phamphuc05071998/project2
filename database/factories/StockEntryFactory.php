<?php
// database/factories/StockEntryFactory.php
namespace Database\Factories;

use App\Models\StockEntry;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockEntryFactory extends Factory
{
    protected $model = StockEntry::class;

    public function definition()
    {
        return [
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
            'approved' => $this->faker->boolean,
        ];
    }
}
