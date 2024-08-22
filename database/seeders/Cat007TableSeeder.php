<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat007TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_007')->delete();
        
        \DB::table('cat_007')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Físico',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Electrónico',
            ),
        ));
        
        
    }
}