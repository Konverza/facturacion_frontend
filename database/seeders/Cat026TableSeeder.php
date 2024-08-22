<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat026TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_026')->delete();
        
        \DB::table('cat_026')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Efectivo',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Bien',
            ),
            2 => 
            array (
                'codigo' => '3',
                'valores' => 'Servicio',
            ),
        ));
        
        
    }
}