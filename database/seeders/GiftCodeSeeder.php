<?php

namespace Database\Seeders;

use App\Models\GiftCode;
use Illuminate\Database\Seeder;

class GiftCodeSeeder extends Seeder
{
    public function run(): void
    {
        GiftCode::factory()->count(50)->create();
    }
}
