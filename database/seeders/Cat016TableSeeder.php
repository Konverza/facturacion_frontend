<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat016TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_016')->delete();
        
        \DB::table('cat_016')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'contado',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'a credito',
            ),
            2 => 
            array (
                'codigo' => '3',
                'valores' => 'otro',
            ),
        ));
        
        
    }
}