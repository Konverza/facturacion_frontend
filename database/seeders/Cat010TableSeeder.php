<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat010TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_010')->delete();
        
        \DB::table('cat_010')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Cirugía',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Operación',
            ),
            2 => 
            array (
                'codigo' => '3',
                'valores' => 'Tratamiento médico',
            ),
            3 => 
            array (
                'codigo' => '4',
                'valores' => 'Cirugía instituto salvadoreño de Bienestar Magisterial',
            ),
            4 => 
            array (
                'codigo' => '5',
                'valores' => 'Operación Instituto Salvadoreño de Bienestar Magisterial',
            ),
            5 => 
            array (
                'codigo' => '6',
                'valores' => 'Tratamiento médico Instituto Salvadoreño de Bienestar Magisterial',
            ),
        ));
        
        
    }
}