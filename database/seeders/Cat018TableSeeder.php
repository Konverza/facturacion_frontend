<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat018TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_018')->delete();
        
        \DB::table('cat_018')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'dias',
            ),
            1 => 
            array (
                'codigo' => '02',
                'valores' => 'meses',
            ),
            2 => 
            array (
                'codigo' => '03',
                'valores' => 'anos',
            ),
        ));
        
        
    }
}