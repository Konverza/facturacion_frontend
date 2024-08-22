<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat011TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_011')->delete();
        
        \DB::table('cat_011')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Bienes',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Servicios',
            ),
            2 => 
            array (
                'codigo' => '3',
            'valores' => 'Ambos (Bienes y Servicios, incluye los dos inherente a los Productos o servicios)',
            ),
            3 => 
            array (
                'codigo' => '4',
                'valores' => 'Otros tributos por ítem',
            ),
        ));
        
        
    }
}