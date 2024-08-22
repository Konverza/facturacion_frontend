<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat009TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_009')->delete();
        
        \DB::table('cat_009')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'Sucursal / Agencia',
            ),
            1 => 
            array (
                'codigo' => '02',
                'valores' => 'Casa matriz',
            ),
            2 => 
            array (
                'codigo' => '04',
                'valores' => 'Bodega',
            ),
            3 => 
            array (
                'codigo' => '07',
                'valores' => 'Predio y/o patio',
            ),
            4 => 
            array (
                'codigo' => '20',
                'valores' => 'Otro',
            ),
        ));
        
        
    }
}