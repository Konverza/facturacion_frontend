<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat003TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_003')->delete();
        
        \DB::table('cat_003')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Modelo Facturación previo',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Modelo Facturación diferido',
            ),
        ));
        
        
    }
}