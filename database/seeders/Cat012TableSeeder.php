<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat012TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_012')->delete();
        
        \DB::table('cat_012')->insert(array (
            0 => 
            array (
                'codigo' => '00',
                'valores' => 'Otro Pais',
            ),
            1 => 
            array (
                'codigo' => '01',
                'valores' => 'Ahuachapán',
            ),
            2 => 
            array (
                'codigo' => '02',
                'valores' => 'Santa Ana',
            ),
            3 => 
            array (
                'codigo' => '03',
                'valores' => 'Sonsonate',
            ),
            4 => 
            array (
                'codigo' => '04',
                'valores' => 'Chalatenango',
            ),
            5 => 
            array (
                'codigo' => '05',
                'valores' => 'La Libertad',
            ),
            6 => 
            array (
                'codigo' => '06',
                'valores' => 'San Salvador',
            ),
            7 => 
            array (
                'codigo' => '07',
                'valores' => 'Cuscatlán',
            ),
            8 => 
            array (
                'codigo' => '08',
                'valores' => 'La Paz',
            ),
            9 => 
            array (
                'codigo' => '09',
                'valores' => 'Cabañas',
            ),
            10 => 
            array (
                'codigo' => '10',
                'valores' => 'San Vicente',
            ),
            11 => 
            array (
                'codigo' => '11',
                'valores' => 'Usulután',
            ),
            12 => 
            array (
                'codigo' => '12',
                'valores' => 'San Miguel',
            ),
            13 => 
            array (
                'codigo' => '13',
                'valores' => 'Morazán',
            ),
            14 => 
            array (
                'codigo' => '14',
                'valores' => 'La Unión',
            ),
        ));
        
        
    }
}