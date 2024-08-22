<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat025TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_025')->delete();
        
        \DB::table('cat_025')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'Deposito',
            ),
            1 => 
            array (
                'codigo' => '02',
                'valores' => 'Propiedad',
            ),
            2 => 
            array (
                'codigo' => '03',
                'valores' => 'Consignation',
            ),
            3 => 
            array (
                'codigo' => '04',
                'valores' => 'Traslado',
            ),
            4 => 
            array (
                'codigo' => '05',
                'valores' => 'Otros',
            ),
        ));
        
        
    }
}