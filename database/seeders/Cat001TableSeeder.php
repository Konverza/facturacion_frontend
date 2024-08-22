<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat001TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_001')->delete();
        
        \DB::table('cat_001')->insert(array (
            0 => 
            array (
                'codigo' => '00',
                'valores' => 'Modo prueba',
            ),
            1 => 
            array (
                'codigo' => '01',
                'valores' => 'Modo producción',
            ),
        ));
        
        
    }
}