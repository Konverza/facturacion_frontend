<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat028TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_028')->delete();
        
        \DB::table('cat_028')->insert(array (
            0 => 
            array (
                'codigo' => 'EX-1.1000.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva, Regimen Comun',
            ),
            1 => 
            array (
                'codigo' => 'EX-1.1040.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Sustitucion de Mercancias, Regimen Comun',
            ),
            2 => 
            array (
                'codigo' => 'EX-1.1041.020',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Provisional, Franq. Presidenciales exento de DAI',
            ),
            3 => 
            array (
                'codigo' => 'EX-1.1041.021',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Provisional, Franq. Presidenciales exento de DAI e IVA',
            ),
            4 => 
            array (
                'codigo' => 'EX-1.1048.025',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Maquinaria y Equipo LZF. DPA',
            ),
            5 => 
            array (
                'codigo' => 'EX-1.1048.031',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Distribucion Internacional',
            ),
            6 => 
            array (
                'codigo' => 'EX-1.1048.032',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Operaciones Internacionales de Logistica',
            ),
            7 => 
            array (
                'codigo' => 'EX-1.1048.033',
            'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Centro Internacional de llamadas (Call Center)',
            ),
            8 => 
            array (
                'codigo' => 'EX-1.1048.034',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Tecnologias de Informacion LSI',
            ),
            9 => 
            array (
                'codigo' => 'EX-1.1048.035',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Investigacion y Desarrollo LSI',
            ),
            10 => 
            array (
                'codigo' => 'EX-1.1048.036',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Reparacion y Mantenimiento de Embarcaciones Maritimas LSI',
            ),
            11 => 
            array (
                'codigo' => 'EX-1.1048.037',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Reparacion y Mantenimiento de Aeronaves LSI',
            ),
            12 => 
            array (
                'codigo' => 'EX-1.1048.038',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Procesos Empresariales LSI',
            ),
            13 => 
            array (
                'codigo' => 'EX-1.1048.039',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Servicios Medico-Hospitalarios LSI',
            ),
            14 => 
            array (
                'codigo' => 'EX-1.1048.040',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Servicios Financieros Internacionales LSI',
            ),
            15 => 
            array (
                'codigo' => 'EX-1.1048.043',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Reparacion y Mantenimiento de Contenedores LSI',
            ),
            16 => 
            array (
                'codigo' => 'EX-1.1048.044',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Reparacion de Equipos Tecnologicos LSI',
            ),
            17 => 
            array (
                'codigo' => 'EX-1.1048.054',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Atencion Ancianos y Convalecientes LSI',
            ),
            18 => 
            array (
                'codigo' => 'EX-1.1048.055',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Telemedicina LSI',
            ),
            19 => 
            array (
                'codigo' => 'EX-1.1048.056',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Proveniente de Franquicia Definitiva, Cinematografia LSI',
            ),
            20 => 
            array (
                'codigo' => 'EX-1.1052.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva de DPA con origen en Compras Locales, Regimen Comun',
            ),
            21 => 
            array (
                'codigo' => 'EX-1.1054.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva de Zona Franca con origen en Compras Locales, Regimen Comun',
            ),
            22 => 
            array (
                'codigo' => 'EX-1.1100.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva de Envíos de Socorro, Regimen Comun',
            ),
            23 => 
            array (
                'codigo' => 'EX-1.1200.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva de Envíos Postales, Regimen Comun',
            ),
            24 => 
            array (
                'codigo' => 'EX-1.1300.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Envíos que requieren despacho urgente, Regimen Comun',
            ),
            25 => 
            array (
                'codigo' => 'EX-1.1400.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Courier, Regimen Comun',
            ),
            26 => 
            array (
                'codigo' => 'EX-1.1400.011',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Courier, Muestras Sin Valor Comercial',
            ),
            27 => 
            array (
                'codigo' => 'EX-1.1400.012',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Courier, Material Publicitario',
            ),
            28 => 
            array (
                'codigo' => 'EX-1.1400.017',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Courier, Declaracion de Documentos',
            ),
            29 => 
            array (
                'codigo' => 'EX-1.1500.000',
                'valores' => 'Exportacion Definitiva, Exportacion Definitiva Menaje de casa, Regimen Comun',
            ),
            30 => 
            array (
                'codigo' => 'EX-2.2100.000',
                'valores' => 'Exportacion Temporal, Exportacion Temporal para Perfeccionamiento Pasivo, Regimen Comun',
            ),
            31 => 
            array (
                'codigo' => 'EX-2.2200.000',
                'valores' => 'Exportacion Temporal, Exportacion Temporal con Reimportacion en el mismo estado, Regimen Comun',
            ),
            32 => 
            array (
                'codigo' => 'EX-3.3050.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Importacion Temporal, Regimen Comun',
            ),
            33 => 
            array (
                'codigo' => 'EX-3.3051.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Tiendas Libres, Regimen Comun',
            ),
            34 => 
            array (
                'codigo' => 'EX-3.3052.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal para Perfeccionamiento Activo, Regimen Comun',
            ),
            35 => 
            array (
                'codigo' => 'EX-3.3053.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal, Regimen Comun',
            ),
            36 => 
            array (
                'codigo' => 'EX-3.3054.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Regimen de Zona Franca, Regimen Comun',
            ),
            37 => 
            array (
                'codigo' => 'EX-3.3055.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal para Perfeccionamiento Activo con Garantia, Regimen Comun',
            ),
            38 => 
            array (
                'codigo' => 'EX-3.3056.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Distribucion Internacional Parque de Servicios, Regimen Comun',
            ),
            39 => 
            array (
                'codigo' => 'EX-3.3056.057',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Distribucion Internacional Parque de Servicios, Remision entre Usuarios Directos del Mismo Parque de Servicios',
            ),
            40 => 
            array (
                'codigo' => 'EX-3.3056.058',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Distribucion Internacional Parque de Servicios, Remision entre Usuarios Directos de Diferente Parque de Servicios',
            ),
            41 => 
            array (
                'codigo' => 'EX-3.3056.072',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Distribucion Internacional Parque de Servicios, Decreto 738 Electricos e Hibridos',
            ),
            42 => 
            array (
                'codigo' => 'EX-3.3057.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Operaciones Internacional de Logistica Parque de Servicios, Regimen Comun',
            ),
            43 => 
            array (
                'codigo' => 'EX-3.3057.057',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Operaciones Internacional de Logistica Parque de Servicios, Remision entre Usuarios Directos del Mismo Parque de Servicios',
            ),
            44 => 
            array (
                'codigo' => 'EX-3.3057.058',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Operaciones Internacional de Logistica Parque de Servicios, Remision entre Usuarios Directos de Diferente Parque de Servicios',
            ),
            45 => 
            array (
                'codigo' => 'EX-3.3058.033',
            'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Centro Servicio LSI, Centro Internacional de llamadas (Call Center)',
            ),
            46 => 
            array (
                'codigo' => 'EX-3.3058.036',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Centro Servicio LSI, Reparacion y Mantenimiento de Embarcaciones Maritimas LSI',
            ),
            47 => 
            array (
                'codigo' => 'EX-3.3058.037',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Centro Servicio LSI, Reparacion y Mantenimiento de Aeronaves LSI',
            ),
            48 => 
            array (
                'codigo' => 'EX-3.3058.043',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Centro Servicio LSI, Reparacion y Mantenimiento de Contenedores LSI',
            ),
            49 => 
            array (
                'codigo' => 'EX-3.3059.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Reparacion de Equipo Tecnologico Parque de Servicios, Regimen Comun',
            ),
            50 => 
            array (
                'codigo' => 'EX-3.3059.057',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Reparacion de Equipo Tecnologico Parque de Servicios, Remision entre Usuarios Directos del Mismo Parque de Servicios',
            ),
            51 => 
            array (
                'codigo' => 'EX-3.3059.058',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Admission Temporal Reparacion de Equipo Tecnologico Parque de Servicios, Remision entre Usuarios Directos de Diferente Parque de Servicios',
            ),
            52 => 
            array (
                'codigo' => 'EX-3.3070.000',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Deposito., Regimen Comun',
            ),
            53 => 
            array (
                'codigo' => 'EX-3.3070.072',
                'valores' => 'Re-Exportacion, Reexportacion Proveniente de Deposito., Decreto 738 Electricos e Hibridos',
            ),
        ));
        
        
    }
}