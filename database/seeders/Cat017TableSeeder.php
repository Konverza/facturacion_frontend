<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat017TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_017')->delete();
        
        \DB::table('cat_017')->insert(array (
            0 => 
            array (
                'codigo' => '01',
                'valores' => 'billetes y monedas',
            ),
            1 => 
            array (
                'codigo' => '02',
                'valores' => 'tarjeta debito',
            ),
            2 => 
            array (
                'codigo' => '03',
                'valores' => 'tarjeta credito',
            ),
            3 => 
            array (
                'codigo' => '04',
                'valores' => 'cheque',
            ),
            4 => 
            array (
                'codigo' => '05',
                'valores' => 'transferencia deposito bancario',
            ),
            5 => 
            array (
                'codigo' => '06',
                'valores' => 'vales o cupones',
            ),
            6 => 
            array (
                'codigo' => '08',
                'valores' => 'dinero electronico',
            ),
            7 => 
            array (
                'codigo' => '09',
                'valores' => 'monedero electronico',
            ),
            8 => 
            array (
                'codigo' => '10',
                'valores' => 'certificado o tarjeta de regalo',
            ),
            9 => 
            array (
                'codigo' => '11',
                'valores' => 'bitcoin',
            ),
            10 => 
            array (
                'codigo' => '12',
                'valores' => 'otras criptomonedas',
            ),
            11 => 
            array (
                'codigo' => '13',
                'valores' => 'cuentas por pagar del receptor',
            ),
            12 => 
            array (
                'codigo' => '14',
                'valores' => 'giro bancario',
            ),
            13 => 
            array (
                'codigo' => '99',
            'valores' => 'otros (se debe indicar el medio de pago)',
            ),
        ));
        
        
    }
}