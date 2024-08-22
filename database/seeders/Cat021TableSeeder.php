<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat021TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_021')->delete();
        
        \DB::table('cat_021')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Emisor',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Receptor',
            ),
            2 => 
            array (
                'codigo' => '3',
            'valores' => 'Medico (solo aplica para contribuyentes obligados a la presentacion de F-958)',
            ),
            3 => 
            array (
                'codigo' => '4',
            'valores' => 'Transporte (solo aplica para Factura de exportacion)',
            ),
        ));
        
        
    }
}