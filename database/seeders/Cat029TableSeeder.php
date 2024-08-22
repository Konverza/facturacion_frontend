<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat029TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_029')->delete();
        
        \DB::table('cat_029')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'Persona Natural',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'Persona Juridica',
            ),
        ));
        
        
    }
}