<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat022TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_022')->delete();
        
        \DB::table('cat_022')->insert(array (
            0 => 
            array (
                'codigo' => '36',
                'valores' => 'NIT',
            ),
            1 => 
            array (
                'codigo' => '13',
                'valores' => 'DUI',
            ),
            2 => 
            array (
                'codigo' => '37',
                'valores' => 'Otro',
            ),
            3 => 
            array (
                'codigo' => '03',
                'valores' => 'Pasaporte',
            ),
            4 => 
            array (
                'codigo' => '02',
                'valores' => 'Carnet de Residente',
            ),
        ));
        
        
    }
}