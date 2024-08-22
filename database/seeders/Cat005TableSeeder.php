<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat005TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_005')->delete();
        
        \DB::table('cat_005')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'No disponibilidad de sistema del MH',
            ),
            1 => 
            array (
                'codigo' => '2',
                'valores' => 'No disponibilidad de sistema del emisor',
            ),
            2 => 
            array (
                'codigo' => '3',
                'valores' => 'Falla en el suministro de servicio de Internet del Emisor',
            ),
            3 => 
            array (
                'codigo' => '4',
                'valores' => 'Falla en el suministro de servicio de energía eléctrica del emisor que impida la transmisión de los DTE',
            ),
            4 => 
            array (
                'codigo' => '5',
            'valores' => 'Otro (deberá digitar un máximo de 500 caracteres explicando el motivo)',
            ),
        ));
        
        
    }
}