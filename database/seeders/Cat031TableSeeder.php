<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat031TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_031')->delete();
        
        \DB::table('cat_031')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'EXW-En fabrica',
            ),
            1 => 
            array (
                'codigo' => '02',
                'valores' => 'FCA-Libre transportista',
            ),
            2 => 
            array (
                'codigo' => '03',
                'valores' => 'CPT-Transporte pagado hasta',
            ),
            3 => 
            array (
                'codigo' => '04',
                'valores' => 'CIP-Transporte y seguro pagado hasta',
            ),
            4 => 
            array (
                'codigo' => '05',
                'valores' => 'DAP-Entrega en el lugar',
            ),
            5 => 
            array (
                'codigo' => '06',
                'valores' => 'DPU-Entregado en el lugar descargado',
            ),
            6 => 
            array (
                'codigo' => '07',
                'valores' => 'DDP-Entrega con impuestos pagados',
            ),
            7 => 
            array (
                'codigo' => '08',
                'valores' => 'FAS-Libre al costado del buque',
            ),
            8 => 
            array (
                'codigo' => '09',
                'valores' => 'FOB-Libre a bordo',
            ),
            9 => 
            array (
                'codigo' => '10',
                'valores' => 'CFR-Costo y flete',
            ),
            10 => 
            array (
                'codigo' => '11',
                'valores' => 'CIF-Costo seguro y flete',
            ),
            11 => 
            array (
                'codigo' => '12',
                'valores' => 'DAT-Entregado en terminal',
            ),
            12 => 
            array (
                'codigo' => '13',
                'valores' => 'DAF-Entregada en frontera',
            ),
            13 => 
            array (
                'codigo' => '14',
                'valores' => 'DES-Entregada sobre duque',
            ),
            14 => 
            array (
                'codigo' => '15',
                'valores' => 'DEQ-Entregada en muelle',
            ),
            15 => 
            array (
                'codigo' => '16',
                'valores' => 'DDU-Entregada derechos no pagados',
            ),
        ));
        
        
    }
}