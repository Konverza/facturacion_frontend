<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat015TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_015')->delete();
        
        \DB::table('cat_015')->insert(array (
            0 => 
            array (
                'codigo' => '1',
                'valores' => 'tributos aplicados por items reflejados en el resumen del dte',
            ),
            1 => 
            array (
                'codigo' => '20',
                'valores' => 'impuesto al valor agregado 13%',
            ),
            2 => 
            array (
                'codigo' => 'C3',
            'valores' => 'impuesto al valor agregado (exportaciones) 0%',
            ),
            3 => 
            array (
                'codigo' => '59',
            'valores' => 'turismo: por alojamiento (5%)',
            ),
            4 => 
            array (
                'codigo' => '71',
                'valores' => 'turismo: salida del pais por via aerea $7.00',
            ),
            5 => 
            array (
                'codigo' => 'D1',
            'valores' => 'fovial ($0.20 ctvs. por galon)',
            ),
            6 => 
            array (
                'codigo' => 'C8',
            'valores' => 'cotrans ($0.10 ctvs. por galon)',
            ),
            7 => 
            array (
                'codigo' => 'D5',
                'valores' => 'otras tasas casos especiales',
            ),
            8 => 
            array (
                'codigo' => 'D4',
                'valores' => 'otros impuestos casos especiales',
            ),
            9 => 
            array (
                'codigo' => '2',
                'valores' => 'tributos aplicados por items reflejados en el cuerpo del documento',
            ),
            10 => 
            array (
                'codigo' => 'A8',
            'valores' => 'impuesto especial al combustible (0%, 0.5%, 1%)',
            ),
            11 => 
            array (
                'codigo' => '57',
                'valores' => 'impuesto industria de cemento',
            ),
            12 => 
            array (
                'codigo' => '90',
                'valores' => 'impuesto especial a la primera matricula',
            ),
            13 => 
            array (
                'codigo' => 'D4',
                'valores' => 'otros impuestos casos especiales',
            ),
            14 => 
            array (
                'codigo' => 'D5',
                'valores' => 'otras tasas casos especiales',
            ),
            15 => 
            array (
                'codigo' => 'A6',
                'valores' => 'impuesto ad-valorem, armas de fuego, municiones explosivas y articulos similares',
            ),
            16 => 
            array (
                'codigo' => '3',
                'valores' => 'impuestos ad-valorem aplicados por item de uso informativo reflejados el resumen del documento.',
            ),
            17 => 
            array (
                'codigo' => 'C5',
            'valores' => 'impuesto ad-valorem por diferencial de precios de bebidas alcoholicas (8%)',
            ),
            18 => 
            array (
                'codigo' => 'C6',
            'valores' => 'impuesto ad-valorem por diferencial de precios al tabaco cigarrillos (39%)',
            ),
            19 => 
            array (
                'codigo' => 'C7',
            'valores' => 'impuesto ad-valorem por diferencial de precios al tabaco cigarros (100%)',
            ),
            20 => 
            array (
                'codigo' => '19',
                'valores' => 'fabricante de bebidas gaseosas, isotónicas, deportivas, fortificantes, energizante o estimulante',
            ),
            21 => 
            array (
                'codigo' => '28',
                'valores' => 'importador de bebidas gaseosas, isotónicas, deportivas, fortificantes, energizante o estimulante',
            ),
            22 => 
            array (
                'codigo' => '31',
                'valores' => 'detallistas o expendedores de bebidas alcoholicas',
            ),
            23 => 
            array (
                'codigo' => '32',
                'valores' => 'fabricante de cerveza',
            ),
            24 => 
            array (
                'codigo' => '33',
                'valores' => 'importador de cerveza',
            ),
            25 => 
            array (
                'codigo' => '34',
                'valores' => 'fabricante de productos de tabaco',
            ),
            26 => 
            array (
                'codigo' => '35',
                'valores' => 'importador de productos de tabaco',
            ),
            27 => 
            array (
                'codigo' => '36',
                'valores' => 'fabricante de armas de fuego, municiones y articulos similares',
            ),
            28 => 
            array (
                'codigo' => '37',
                'valores' => 'importador de arma de fuego, municion y articulos similares',
            ),
            29 => 
            array (
                'codigo' => '38',
                'valores' => 'fabricante de explosivos',
            ),
            30 => 
            array (
                'codigo' => '39',
                'valores' => 'importador de explosivos',
            ),
            31 => 
            array (
                'codigo' => '42',
                'valores' => 'fabricante de productos pirotecnicos',
            ),
            32 => 
            array (
                'codigo' => '43',
                'valores' => 'importador de productos pirotecnicos',
            ),
            33 => 
            array (
                'codigo' => '44',
                'valores' => 'productor de tabaco',
            ),
            34 => 
            array (
                'codigo' => '50',
                'valores' => 'distribuidor de bebidas gaseosas, isotónicas, deportivas, fortificantes, energizante o estimulante',
            ),
            35 => 
            array (
                'codigo' => '51',
                'valores' => 'bebidas alcoholicas',
            ),
            36 => 
            array (
                'codigo' => '52',
                'valores' => 'cerveza',
            ),
            37 => 
            array (
                'codigo' => '53',
                'valores' => 'productos del tabaco',
            ),
            38 => 
            array (
                'codigo' => '54',
                'valores' => 'bebidas carbonatadas o gaseosas simples o endulzadas',
            ),
            39 => 
            array (
                'codigo' => '55',
                'valores' => 'otros especificos',
            ),
            40 => 
            array (
                'codigo' => '58',
                'valores' => 'alcohol',
            ),
            41 => 
            array (
                'codigo' => '77',
                'valores' => 'importador de jugos, nectar, bebidas con jugo y refrescos',
            ),
            42 => 
            array (
                'codigo' => '78',
                'valores' => 'distribuidor de jugos, nectar, bebidas con jugo y refrescos',
            ),
            43 => 
            array (
                'codigo' => '79',
                'valores' => 'sobre llamadas telefonicas provenientes del ext.',
            ),
            44 => 
            array (
                'codigo' => '85',
                'valores' => 'detallista de jugos, nectar, bebidas con jugo y refrescos',
            ),
            45 => 
            array (
                'codigo' => '86',
                'valores' => 'fabricante de preparaciones concentradas o en polvo para la elaboracion de bebidas',
            ),
            46 => 
            array (
                'codigo' => '91',
                'valores' => 'fabricante de jugos, nectar, bebidas con jugo y refrescos',
            ),
            47 => 
            array (
                'codigo' => '92',
                'valores' => 'importador de preparaciones concentradas o en polvo para la elaboracion de bebidas',
            ),
            48 => 
            array (
                'codigo' => 'A1',
                'valores' => 'especificos y ad-valorem',
            ),
            49 => 
            array (
                'codigo' => 'A5',
                'valores' => 'bebidas gaseosas, isotónicas, deportivas, fortificantes, energizantes o estimulantes',
            ),
            50 => 
            array (
                'codigo' => 'A7',
                'valores' => 'alcohol etilico',
            ),
            51 => 
            array (
                'codigo' => 'A9',
                'valores' => 'sacos sinteticos',
            ),
        ));
        
        
    }
}