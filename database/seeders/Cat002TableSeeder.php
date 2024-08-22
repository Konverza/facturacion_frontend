<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat002TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_002')->delete();
        
        \DB::table('cat_002')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'Factura',
            ),
            1 => 
            array (
                'codigo' => '03',
                'valores' => 'Comprobante de crédito fiscal',
            ),
            2 => 
            array (
                'codigo' => '04',
                'valores' => 'Nota de remisión',
            ),
            3 => 
            array (
                'codigo' => '05',
                'valores' => 'Nota de crédito',
            ),
            4 => 
            array (
                'codigo' => '06',
                'valores' => 'Nota de débito',
            ),
            5 => 
            array (
                'codigo' => '07',
                'valores' => 'Comprobante de retención',
            ),
            6 => 
            array (
                'codigo' => '08',
                'valores' => 'Comprobante de liquidación',
            ),
            7 => 
            array (
                'codigo' => '09',
                'valores' => 'Documento contable de liquidación',
            ),
            8 => 
            array (
                'codigo' => '11',
                'valores' => 'Facturas de exportación',
            ),
            9 => 
            array (
                'codigo' => '14',
                'valores' => 'Factura de sujeto excluido',
            ),
            10 => 
            array (
                'codigo' => '15',
                'valores' => 'Comprobante de donación',
            ),
        ));
        
        
    }
}