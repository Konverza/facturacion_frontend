<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat032TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_032')->delete();
        
        \DB::table('cat_032')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Domiciliado',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'No Domiciliado',
            ),
        ));
        
        
    }
}