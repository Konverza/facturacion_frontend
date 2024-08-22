<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat006TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_006')->delete();
        
        \DB::table('cat_006')->insert(array (
            0 => 
            array (
                'codigo' => '22',
                'valores' => 'Retención IVA 1%',
            ),
            1 => 
            array (
                'codigo' => 'C4',
                'valores' => 'Retención IVA 13%',
            ),
            2 => 
            array (
                'codigo' => 'C9',
                'valores' => 'Otras retenciones IVA casos especiales',
            ),
        ));
        
        
    }
}