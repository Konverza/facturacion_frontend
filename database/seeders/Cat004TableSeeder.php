<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat004TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_004')->delete();
        
        \DB::table('cat_004')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Transmisión normal',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Transmisión por contingencia',
            ),
        ));
        
        
    }
}