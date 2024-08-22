<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            "nombre" => "Plan Emprendedor",
            "limite" => 50,
            "precio" => 29.99,
            "precio_adicional" => 0.20,
        ]);
        Plan::create([
            "nombre" => "Plan Cloud #1",
            "limite" => 100,
            "precio" => 49.99,
            "precio_adicional" => 0.15,
        ]);
        Plan::create([
            "nombre" => "Plan Cloud #2",
            "limite" => 200,
            "precio" => 79.99,
            "precio_adicional" => 0.1,
        ]);
    }
}
