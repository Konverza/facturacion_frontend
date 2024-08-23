<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tributes;
class TributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tributes::create([
            "codigo" => "20",
            "descripcion" => "Impuesto al Valor Agregado 13%",
            "valor" => 0.13,
            "es_porcentaje" => true,
            "aplicar_a_cantidad" => false,
        ]);
        Tributes::create([
            "codigo" => "59",
            "descripcion" => "Turismo: Por Alojamiento (5%)",
            "valor" => 0.05,
            "es_porcentaje" => true,
            "aplicar_a_cantidad" => false,
        ]);
        Tributes::create([
            "codigo" => "71",
            "descripcion" => "Turismo: Salida del País Por Vía Aérea",
            "valor" => 7,
            "es_porcentaje" => false,
            "aplicar_a_cantidad" => false,
        ]);
        Tributes::create([
            "codigo" => "D1",
            "descripcion" => "FOVIAL ($0.20 por galón de combustible)",
            "valor" => 0.20,
            "es_porcentaje" => false,
            "aplicar_a_cantidad" => true,
        ]);
        Tributes::create([
            "codigo" => "C8",
            "descripcion" => "COTRANS ($0.10 por galón de combustible)",
            "valor" => 0.10,
            "es_porcentaje" => false,
            "aplicar_a_cantidad" => true,
        ]);
        Tributes::create([
            "codigo" => "C5",
            "descripcion" => "Impuesto ad-valorem por diferencial de precio de Bebidas Alcohólicas (8%)",
            "valor" => 0.08,
            "es_porcentaje" => true,
            "aplicar_a_cantidad" => false,
        ]);
        Tributes::create([
            "codigo" => "C6",
            "descripcion" => "Impuesto ad-valorem por diferencial de precio al tabaco cigarrillos (39%)",
            "valor" => 0.39,
            "es_porcentaje" => true,
            "aplicar_a_cantidad" => false,
        ]);
        Tributes::create([
            "codigo" => "C7",
            "descripcion" => "Impuesto ad-valorem por diferencial de precio al tabaco cigarros (100%)",
            "valor" => 1,
            "es_porcentaje" => true,
            "aplicar_a_cantidad" => false,
        ]);
    }
}
