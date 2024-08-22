<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat023TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_023')->delete();
        
        \DB::table('cat_023')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'Factura Electronico',
            ),
            1 => 
            array (
                'codigo' => '03',
                'valores' => 'Comprobante de Credito Fiscal Electronico',
            ),
            2 => 
            array (
                'codigo' => '04',
                'valores' => 'Nota de Remision Electronica',
            ),
            3 => 
            array (
                'codigo' => '05',
                'valores' => 'Nota de Credito Electronica',
            ),
            4 => 
            array (
                'codigo' => '06',
                'valores' => 'Nota de Debito Electronica',
            ),
            5 => 
            array (
                'codigo' => '11',
                'valores' => 'Factura de Exportacion Electronica',
            ),
            6 => 
            array (
                'codigo' => '14',
                'valores' => 'Factura de Sujeto Excluido Electronica',
            ),
        ));
        
        
    }
}