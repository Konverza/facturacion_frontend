<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat024TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_024')->delete();
        
        \DB::table('cat_024')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Error en la Informacion del Documento Tributario Electronico a invalidar.',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Rescindir de la operacion realizada.',
            ),
            2 => 
            array (
                'codigo' => '3',
                'valores' => 'Otro',
            ),
        ));
        
        
    }
}