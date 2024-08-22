<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat030TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_030')->delete();
        
        \DB::table('cat_030')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Terrestre',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Maritimo',
            ),
            2 => 
            array (
                'codigo' => '3',
                'valores' => 'Aereo',
            ),
            3 => 
            array (
                'codigo' => '4',
                'valores' => 'Multimodal, Terrestre-marítimo',
            ),
            4 => 
            array (
                'codigo' => '5',
                'valores' => 'Multimodal, Terrestre-aéreo',
            ),
            5 => 
            array (
                'codigo' => '6',
                'valores' => 'Multimodal, Marítimo-aéreo',
            ),
            6 => 
            array (
                'codigo' => '7',
                'valores' => 'Multimodal, Terrestre-Marítimo-aéreo',
            ),
        ));
        
        
    }
}