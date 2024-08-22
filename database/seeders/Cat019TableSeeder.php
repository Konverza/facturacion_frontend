<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat019TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_019')->delete();
        
        \DB::table('cat_019')->insert(array (
            0 => 
            array (
                'codigo' => '01111',
                'valores' => 'cultivo de cereales excepto arroz y para forrajes',
            ),
            1 => 
            array (
                'codigo' => '01112',
                'valores' => 'cultivo de legumbres',
            ),
            2 => 
            array (
                'codigo' => '01113',
                'valores' => 'cultivo de semillas oleaginosas',
            ),
            3 => 
            array (
                'codigo' => '01114',
                'valores' => 'cultivo de plantas para la preparación de semillas',
            ),
            4 => 
            array (
                'codigo' => '01119',
                'valores' => 'cultivo de otros cereales excepto arroz y forrajeros n.c.p.',
            ),
            5 => 
            array (
                'codigo' => '01120',
                'valores' => 'cultivo de arroz',
            ),
            6 => 
            array (
                'codigo' => '01131',
                'valores' => 'cultivo de raíces y tubérculos',
            ),
            7 => 
            array (
                'codigo' => '01132',
                'valores' => 'cultivo de brotes, bulbos, vegetales tubérculos y cultivos similares',
            ),
            8 => 
            array (
                'codigo' => '01133',
                'valores' => 'cultivo hortícola de fruto',
            ),
            9 => 
            array (
                'codigo' => '01134',
                'valores' => 'cultivo de hortalizas de hoja y otras hortalizas ncp',
            ),
            10 => 
            array (
                'codigo' => '01140',
                'valores' => 'cultivo de caña de azúcar',
            ),
            11 => 
            array (
                'codigo' => '01150',
                'valores' => 'cultivo de tabaco',
            ),
            12 => 
            array (
                'codigo' => '01161',
                'valores' => 'cultivo de algodón',
            ),
            13 => 
            array (
                'codigo' => '01162',
                'valores' => 'cultivo de fibras vegetales excepto algodón',
            ),
            14 => 
            array (
                'codigo' => '01191',
                'valores' => 'cultivo de plantas no perennes para la producción de semillas y flores',
            ),
            15 => 
            array (
                'codigo' => '01192',
                'valores' => 'cultivo de cereales y pastos para la alimentación animal',
            ),
            16 => 
            array (
                'codigo' => '01199',
                'valores' => 'producción de cultivos no estacionales ncp',
            ),
            17 => 
            array (
                'codigo' => '01220',
                'valores' => 'cultivo de frutas tropicales',
            ),
            18 => 
            array (
                'codigo' => '01230',
                'valores' => 'cultivo de cítricos',
            ),
            19 => 
            array (
                'codigo' => '01240',
                'valores' => 'cultivo de frutas de pepita y hueso',
            ),
            20 => 
            array (
                'codigo' => '01251',
                'valores' => 'cultivo de frutas ncp',
            ),
            21 => 
            array (
                'codigo' => '01252',
                'valores' => 'cultivo de otros frutos y nueces de árboles y arbustos',
            ),
            22 => 
            array (
                'codigo' => '01260',
                'valores' => 'cultivo de frutos oleaginosos',
            ),
            23 => 
            array (
                'codigo' => '01271',
                'valores' => 'cultivo de café',
            ),
            24 => 
            array (
                'codigo' => '01272',
                'valores' => 'cultivo de plantas para la elaboración de bebidas excepto café',
            ),
            25 => 
            array (
                'codigo' => '01281',
                'valores' => 'cultivo de especias y aromáticas',
            ),
            26 => 
            array (
                'codigo' => '01282',
                'valores' => 'cultivo de plantas para la obtención de productos medicinales y farmacéuticos',
            ),
            27 => 
            array (
                'codigo' => '01291',
            'valores' => 'cultivo de árboles de hule (caucho) para la obtención de látex',
            ),
            28 => 
            array (
                'codigo' => '01292',
                'valores' => 'cultivo de plantas para la obtención de productos químicos y colorantes',
            ),
            29 => 
            array (
                'codigo' => '01299',
                'valores' => 'producción de cultivos perennes ncp',
            ),
            30 => 
            array (
                'codigo' => '01300',
                'valores' => 'propagación de plantas',
            ),
            31 => 
            array (
                'codigo' => '01301',
                'valores' => 'cultivo de plantas y flores ornamentales',
            ),
            32 => 
            array (
                'codigo' => '01410',
                'valores' => 'cría y engorde de ganado bovino',
            ),
            33 => 
            array (
                'codigo' => '01420',
                'valores' => 'cría de caballos y otros equinos',
            ),
            34 => 
            array (
                'codigo' => '01440',
                'valores' => 'cría de ovejas y cabras',
            ),
            35 => 
            array (
                'codigo' => '01450',
                'valores' => 'cría de cerdos',
            ),
            36 => 
            array (
                'codigo' => '01460',
                'valores' => 'cría de aves de corral y producción de huevos',
            ),
            37 => 
            array (
                'codigo' => '01491',
                'valores' => 'cría de abejas apicultura para la obtención de miel y otros productos apícolas',
            ),
            38 => 
            array (
                'codigo' => '01492',
                'valores' => 'cría de conejos',
            ),
            39 => 
            array (
                'codigo' => '01493',
                'valores' => 'cría de iguanas y garrobos',
            ),
            40 => 
            array (
                'codigo' => '01494',
                'valores' => 'cría de mariposas y otros insectos',
            ),
            41 => 
            array (
                'codigo' => '01499',
                'valores' => 'cría y obtención de productos animales n.c.p.',
            ),
            42 => 
            array (
                'codigo' => '01500',
                'valores' => 'cultivo de productos agrícolas en combinación con la cría de animales',
            ),
            43 => 
            array (
                'codigo' => '01611',
                'valores' => 'servicios de maquinaria agrícola',
            ),
            44 => 
            array (
                'codigo' => '01612',
                'valores' => 'control de plagas',
            ),
            45 => 
            array (
                'codigo' => '01613',
                'valores' => 'servicios de riego',
            ),
            46 => 
            array (
                'codigo' => '01614',
                'valores' => 'servicios de contratación de mano de obra para la agricultura',
            ),
            47 => 
            array (
                'codigo' => '01619',
                'valores' => 'servicios agrícolas ncp',
            ),
            48 => 
            array (
                'codigo' => '01621',
                'valores' => 'actividades para mejorar la reproducción, el crecimiento y el rendimiento de los animales y sus productos',
            ),
            49 => 
            array (
                'codigo' => '01622',
                'valores' => 'servicios de mano de obra pecuaria',
            ),
            50 => 
            array (
                'codigo' => '01629',
                'valores' => 'servicios pecuarios ncp',
            ),
            51 => 
            array (
                'codigo' => '01631',
                'valores' => 'labores post cosecha de preparación de los productos agrícolas para su comercialización o para la industria',
            ),
            52 => 
            array (
                'codigo' => '01632',
                'valores' => 'servicio de beneficio de café',
            ),
            53 => 
            array (
                'codigo' => '01633',
            'valores' => 'servicio de beneficiado de plantas textiles (incluye el beneficiado cuando este es realizado en la misma explotación agropecuaria)',
            ),
            54 => 
            array (
                'codigo' => '01640',
                'valores' => 'tratamiento de semillas para la propagación',
            ),
            55 => 
            array (
                'codigo' => '01700',
                'valores' => 'caza ordinaria y mediante trampas, repoblación de animales de caza y servicios conexos',
            ),
            56 => 
            array (
                'codigo' => '02100',
                'valores' => 'silvicultura y otras actividades forestales',
            ),
            57 => 
            array (
                'codigo' => '02200',
                'valores' => 'extracción de madera',
            ),
            58 => 
            array (
                'codigo' => '02300',
                'valores' => 'recolección de productos diferentes a la madera',
            ),
            59 => 
            array (
                'codigo' => '02400',
                'valores' => 'servicios de apoyo a la silvicultura',
            ),
            60 => 
            array (
                'codigo' => '03110',
                'valores' => 'pesca marítima de altura y costera',
            ),
            61 => 
            array (
                'codigo' => '03120',
                'valores' => 'pesca de agua dulce',
            ),
            62 => 
            array (
                'codigo' => '03210',
                'valores' => 'acuicultura marítima',
            ),
            63 => 
            array (
                'codigo' => '03220',
                'valores' => 'acuicultura de agua dulce',
            ),
            64 => 
            array (
                'codigo' => '03300',
                'valores' => 'servicios de apoyo a la pesca y acuicultura',
            ),
            65 => 
            array (
                'codigo' => '05100',
                'valores' => 'extracción de hulla',
            ),
            66 => 
            array (
                'codigo' => '05200',
                'valores' => 'extracción y aglomeración de lignito',
            ),
            67 => 
            array (
                'codigo' => '06100',
                'valores' => 'extracción de petróleo crudo',
            ),
            68 => 
            array (
                'codigo' => '06200',
                'valores' => 'extracción de gas natural',
            ),
            69 => 
            array (
                'codigo' => '07100',
                'valores' => 'extracción de minerales de hierro',
            ),
            70 => 
            array (
                'codigo' => '07210',
                'valores' => 'extracción de minerales de uranio y torio',
            ),
            71 => 
            array (
                'codigo' => '07290',
                'valores' => 'extracción de minerales metalíferos no ferrosos',
            ),
            72 => 
            array (
                'codigo' => '08100',
                'valores' => 'extracción de piedra, arena y arcilla',
            ),
            73 => 
            array (
                'codigo' => '08910',
                'valores' => 'extracción de minerales para la fabricación de abonos y productos químicos',
            ),
            74 => 
            array (
                'codigo' => '08920',
                'valores' => 'extracción y aglomeración de turba',
            ),
            75 => 
            array (
                'codigo' => '08930',
                'valores' => 'extracción de sal',
            ),
            76 => 
            array (
                'codigo' => '08990',
                'valores' => 'explotación de otras minas y canteras ncp',
            ),
            77 => 
            array (
                'codigo' => '09100',
                'valores' => 'actividades de apoyo a la extracción de petróleo y gas natural',
            ),
            78 => 
            array (
                'codigo' => '09900',
                'valores' => 'actividades de apoyo a la explotación de minas y canteras',
            ),
            79 => 
            array (
                'codigo' => '10101',
                'valores' => 'servicio de rastros y mataderos de bovinos y porcinos',
            ),
            80 => 
            array (
                'codigo' => '10102',
                'valores' => 'matanza y procesamiento de bovinos y porcinos',
            ),
            81 => 
            array (
                'codigo' => '10103',
                'valores' => 'matanza y procesamientos de aves de corral',
            ),
            82 => 
            array (
                'codigo' => '10104',
                'valores' => 'elaboración y conservación de embutidos y tripas naturales',
            ),
            83 => 
            array (
                'codigo' => '10105',
                'valores' => 'servicios de conservación y empaque de carnes',
            ),
            84 => 
            array (
                'codigo' => '10106',
                'valores' => 'elaboración y conservación de grasas y aceites animales',
            ),
            85 => 
            array (
                'codigo' => '10107',
                'valores' => 'servicios de molienda de carne',
            ),
            86 => 
            array (
                'codigo' => '10108',
                'valores' => 'elaboración de productos de carne ncp',
            ),
            87 => 
            array (
                'codigo' => '10201',
                'valores' => 'procesamiento y conservación de pescado, crustáceos y moluscos',
            ),
            88 => 
            array (
                'codigo' => '10209',
                'valores' => 'fabricación de productos de pescado ncp',
            ),
            89 => 
            array (
                'codigo' => '10301',
                'valores' => 'elaboración de jugos de frutas y hortalizas',
            ),
            90 => 
            array (
                'codigo' => '10302',
                'valores' => 'elaboración y envase de jaleas, mermeladas y frutas deshidratadas',
            ),
            91 => 
            array (
                'codigo' => '10309',
                'valores' => 'elaboración de productos de frutas y hortalizas n.c.p.',
            ),
            92 => 
            array (
                'codigo' => '10401',
                'valores' => 'fabricación de aceites y grasas vegetales y animales comestibles',
            ),
            93 => 
            array (
                'codigo' => '10402',
                'valores' => 'fabricación de aceites y grasas vegetales y animales no comestibles',
            ),
            94 => 
            array (
                'codigo' => '10409',
                'valores' => 'servicio de maquilado de aceites',
            ),
            95 => 
            array (
                'codigo' => '10501',
                'valores' => 'fabricación de productos lácteos excepto sorbetes y quesos sustitutos',
            ),
            96 => 
            array (
                'codigo' => '10502',
                'valores' => 'fabricación de sorbetes y helados',
            ),
            97 => 
            array (
                'codigo' => '10503',
                'valores' => 'fabricación de quesos',
            ),
            98 => 
            array (
                'codigo' => '10611',
                'valores' => 'molienda de cereales',
            ),
            99 => 
            array (
                'codigo' => '10612',
                'valores' => 'elaboración de cereales para el desayuno y similares',
            ),
            100 => 
            array (
                'codigo' => '10613',
            'valores' => 'servicios de beneficiado de productos agrícolas ncp (excluye beneficio de azúcar rama 1072 y beneficio de café rama 0163)',
            ),
            101 => 
            array (
                'codigo' => '10621',
                'valores' => 'fabricación de almidón',
            ),
            102 => 
            array (
                'codigo' => '10628',
                'valores' => 'servicio de molienda de maíz húmedo molino para nixtamal',
            ),
            103 => 
            array (
                'codigo' => '10711',
                'valores' => 'elaboración de tortillas',
            ),
            104 => 
            array (
                'codigo' => '10712',
                'valores' => 'fabricación de pan, galletas y barquillos',
            ),
            105 => 
            array (
                'codigo' => '10713',
                'valores' => 'fabricación de repostería',
            ),
            106 => 
            array (
                'codigo' => '10721',
                'valores' => 'ingenios azucareros',
            ),
            107 => 
            array (
                'codigo' => '10722',
                'valores' => 'molienda de caña de azúcar para la elaboración de dulces',
            ),
            108 => 
            array (
                'codigo' => '10723',
                'valores' => 'elaboración de jarabes de azúcar y otros similares',
            ),
            109 => 
            array (
                'codigo' => '10724',
                'valores' => 'maquilado de azúcar de caña',
            ),
            110 => 
            array (
                'codigo' => '10730',
                'valores' => 'fabricación de cacao, chocolates y productos de confitería',
            ),
            111 => 
            array (
                'codigo' => '10740',
                'valores' => 'elaboración de macarrones, fideos, y productos farináceos similares',
            ),
            112 => 
            array (
                'codigo' => '10750',
                'valores' => 'elaboración de comidas y platos preparados para la reventa en locales y/o para exportación',
            ),
            113 => 
            array (
                'codigo' => '10791',
                'valores' => 'elaboración de productos de café',
            ),
            114 => 
            array (
                'codigo' => '10792',
                'valores' => 'elaboración de especies, sazonadores y condimentos',
            ),
            115 => 
            array (
                'codigo' => '10793',
                'valores' => 'elaboración de sopas, cremas y consomé',
            ),
            116 => 
            array (
                'codigo' => '10794',
                'valores' => 'fabricación de bocadillos tostados y/o fritos',
            ),
            117 => 
            array (
                'codigo' => '10799',
                'valores' => 'elaboración de productos alimenticios ncp',
            ),
            118 => 
            array (
                'codigo' => '10800',
                'valores' => 'elaboración de alimentos preparados para animales',
            ),
            119 => 
            array (
                'codigo' => '11012',
                'valores' => 'fabricación de aguardiente y licores',
            ),
            120 => 
            array (
                'codigo' => '11020',
                'valores' => 'elaboración de vinos',
            ),
            121 => 
            array (
                'codigo' => '11030',
                'valores' => 'fabricación de cerveza',
            ),
            122 => 
            array (
                'codigo' => '11041',
                'valores' => 'fabricación de aguas gaseosas',
            ),
            123 => 
            array (
                'codigo' => '11042',
                'valores' => 'fabricación y envasado de agua',
            ),
            124 => 
            array (
                'codigo' => '11043',
                'valores' => 'elaboración de refrescos',
            ),
            125 => 
            array (
                'codigo' => '11048',
                'valores' => 'maquilado de aguas gaseosas',
            ),
            126 => 
            array (
                'codigo' => '11049',
                'valores' => 'elaboración de bebidas no alcohólicas',
            ),
            127 => 
            array (
                'codigo' => '12000',
                'valores' => 'elaboración de productos de tabaco',
            ),
            128 => 
            array (
                'codigo' => '13111',
                'valores' => 'preparación de fibras textiles',
            ),
            129 => 
            array (
                'codigo' => '13112',
                'valores' => 'fabricación de hilados',
            ),
            130 => 
            array (
                'codigo' => '13120',
                'valores' => 'fabricación de telas',
            ),
            131 => 
            array (
                'codigo' => '13130',
                'valores' => 'acabado de productos textiles',
            ),
            132 => 
            array (
                'codigo' => '13910',
                'valores' => 'fabricación de tejidos de punto y ganchillo',
            ),
            133 => 
            array (
                'codigo' => '13921',
                'valores' => 'fabricación de productos textiles para el hogar',
            ),
            134 => 
            array (
                'codigo' => '13922',
                'valores' => 'sacos, bolsas y otros artículos textiles',
            ),
            135 => 
            array (
                'codigo' => '13929',
                'valores' => 'fabricación de artículos confeccionados con materiales textiles, excepto prendas de vestir ncp',
            ),
            136 => 
            array (
                'codigo' => '13930',
                'valores' => 'fabricación de tapices y alfombras',
            ),
            137 => 
            array (
                'codigo' => '13941',
            'valores' => 'fabricación de cuerdas de henequén y otras fibras naturales (lazos, pitas)',
            ),
            138 => 
            array (
                'codigo' => '13942',
                'valores' => 'fabricación de redes de diversos materiales',
            ),
            139 => 
            array (
                'codigo' => '13948',
            'valores' => 'maquilado de productos trenzables de cualquier material (petates, sillas, etc.)',
            ),
            140 => 
            array (
                'codigo' => '13991',
                'valores' => 'fabricación de adornos, etiquetas y otros artículos para prendas de vestir',
            ),
            141 => 
            array (
                'codigo' => '13992',
                'valores' => 'servicio de bordados en artículos y prendas de tela',
            ),
            142 => 
            array (
                'codigo' => '13999',
                'valores' => 'fabricación de productos textiles ncp',
            ),
            143 => 
            array (
                'codigo' => '14101',
                'valores' => 'fabricación de ropa interior, para dormir y similares',
            ),
            144 => 
            array (
                'codigo' => '14102',
                'valores' => 'fabricación de ropa para niños',
            ),
            145 => 
            array (
                'codigo' => '14103',
                'valores' => 'fabricación de prendas de vestir para ambos sexos',
            ),
            146 => 
            array (
                'codigo' => '14104',
                'valores' => 'confección de prendas a medida',
            ),
            147 => 
            array (
                'codigo' => '14105',
                'valores' => 'fabricación de prendas de vestir para deportes',
            ),
            148 => 
            array (
                'codigo' => '14106',
                'valores' => 'elaboración de artesanías de uso personal confeccionadas especialmente de materiales textiles',
            ),
            149 => 
            array (
                'codigo' => '14108',
                'valores' => 'maquilado de prendas de vestir, accesorios y otros',
            ),
            150 => 
            array (
                'codigo' => '14109',
                'valores' => 'fabricación de prendas y accesorios de vestir n.c.p.',
            ),
            151 => 
            array (
                'codigo' => '14200',
                'valores' => 'fabricación de artículos de piel',
            ),
            152 => 
            array (
                'codigo' => '14301',
            'valores' => 'fabricación de calcetines, calcetas, medias (panty house) y otros similares',
            ),
            153 => 
            array (
                'codigo' => '14302',
                'valores' => 'fabricación de ropa interior de tejido de punto',
            ),
            154 => 
            array (
                'codigo' => '14309',
                'valores' => 'fabricación de prendas de vestir de tejido de punto ncp',
            ),
            155 => 
            array (
                'codigo' => '15110',
                'valores' => 'curtido y adobo de cueros; adobo y teñido de pieles',
            ),
            156 => 
            array (
                'codigo' => '15121',
                'valores' => 'fabricación de maletas, bolsos de mano y otros artículos de marroquinería',
            ),
            157 => 
            array (
                'codigo' => '15122',
                'valores' => 'fabricación de monturas, accesorios y vainas talabartería',
            ),
            158 => 
            array (
                'codigo' => '15123',
                'valores' => 'fabricación de artesanías principalmente de cuero natural y sintético',
            ),
            159 => 
            array (
                'codigo' => '15128',
                'valores' => 'maquilado de artículos de cuero natural, sintético y de otros materiales',
            ),
            160 => 
            array (
                'codigo' => '15201',
                'valores' => 'fabricación de calzado',
            ),
            161 => 
            array (
                'codigo' => '15202',
                'valores' => 'fabricación de partes y accesorios de calzado',
            ),
            162 => 
            array (
                'codigo' => '15208',
                'valores' => 'maquilado de partes y accesorios de calzado',
            ),
            163 => 
            array (
                'codigo' => '16100',
                'valores' => 'aserradero y acepilladura de madera',
            ),
            164 => 
            array (
                'codigo' => '16210',
                'valores' => 'fabricación de madera laminada, terciada, enchapada y contrachapada, paneles para la construcción',
            ),
            165 => 
            array (
                'codigo' => '16220',
                'valores' => 'fabricación de partes y piezas de carpintería para edificios y construcciones',
            ),
            166 => 
            array (
                'codigo' => '16230',
                'valores' => 'fabricación de envases y recipientes de madera',
            ),
            167 => 
            array (
                'codigo' => '16292',
                'valores' => 'fabricación de artesanías de madera, semillas, materiales trenzables',
            ),
            168 => 
            array (
                'codigo' => '16299',
                'valores' => 'fabricación de productos de madera, corcho, paja y materiales trenzables ncp',
            ),
            169 => 
            array (
                'codigo' => '17010',
                'valores' => 'fabricación de pasta de madera, papel y cartón',
            ),
            170 => 
            array (
                'codigo' => '17020',
                'valores' => 'fabricación de papel y cartón ondulado y envases de papel y cartón',
            ),
            171 => 
            array (
                'codigo' => '17091',
                'valores' => 'fabricación de artículos de papel y cartón de uso personal y doméstico',
            ),
            172 => 
            array (
                'codigo' => '17092',
                'valores' => 'fabricación de productos de papel ncp',
            ),
            173 => 
            array (
                'codigo' => '18110',
                'valores' => 'impresión',
            ),
            174 => 
            array (
                'codigo' => '18120',
                'valores' => 'servicios relacionados con la impresión',
            ),
            175 => 
            array (
                'codigo' => '18200',
                'valores' => 'reproducción de grabaciones',
            ),
            176 => 
            array (
                'codigo' => '19100',
                'valores' => 'fabricación de productos de hornos de coque',
            ),
            177 => 
            array (
                'codigo' => '19201',
                'valores' => 'fabricación de combustible',
            ),
            178 => 
            array (
                'codigo' => '19202',
                'valores' => 'fabricación de aceites y lubricantes',
            ),
            179 => 
            array (
                'codigo' => '20111',
                'valores' => 'fabricación de materias primas para la fabricación de colorantes',
            ),
            180 => 
            array (
                'codigo' => '20112',
                'valores' => 'fabricación de materiales curtientes',
            ),
            181 => 
            array (
                'codigo' => '20113',
                'valores' => 'fabricación de gases industriales',
            ),
            182 => 
            array (
                'codigo' => '20114',
                'valores' => 'fabricación de alcohol etílico',
            ),
            183 => 
            array (
                'codigo' => '20119',
                'valores' => 'fabricación de sustancias químicas básicas',
            ),
            184 => 
            array (
                'codigo' => '20120',
                'valores' => 'fabricación de abonos y fertilizantes',
            ),
            185 => 
            array (
                'codigo' => '20130',
                'valores' => 'fabricación de plástico y caucho en formas primarias',
            ),
            186 => 
            array (
                'codigo' => '20210',
                'valores' => 'fabricación de plaguicidas y otros productos químicos de uso agropecuario',
            ),
            187 => 
            array (
                'codigo' => '20220',
                'valores' => 'fabricación de pinturas, barnices y productos de revestimiento similares; tintas de imprenta y masillas',
            ),
            188 => 
            array (
                'codigo' => '20231',
                'valores' => 'fabricación de jabones, detergentes y similares para limpieza',
            ),
            189 => 
            array (
                'codigo' => '20232',
                'valores' => 'fabricación de perfumes, cosméticos y productos de higiene y cuidado personal, incluyendo tintes, champú, etc.',
            ),
            190 => 
            array (
                'codigo' => '20291',
                'valores' => 'fabricación de tintas y colores para escribir y pintar; fabricación de cintas para impresoras',
            ),
            191 => 
            array (
                'codigo' => '20292',
                'valores' => 'fabricación de productos pirotécnicos, explosivos y municiones',
            ),
            192 => 
            array (
                'codigo' => '20299',
                'valores' => 'fabricación de productos químicos n.c.p.',
            ),
            193 => 
            array (
                'codigo' => '20300',
                'valores' => 'fabricación de fibras artificiales',
            ),
            194 => 
            array (
                'codigo' => '21001',
                'valores' => 'manufactura de productos farmacéuticos, sustancias químicas y productos botánicos',
            ),
            195 => 
            array (
                'codigo' => '21008',
                'valores' => 'maquilado de medicamentos',
            ),
            196 => 
            array (
                'codigo' => '22110',
                'valores' => 'fabricación de cubiertas y cámaras; renovación y recauchutado de cubiertas',
            ),
            197 => 
            array (
                'codigo' => '22190',
                'valores' => 'fabricación de otros productos de caucho',
            ),
            198 => 
            array (
                'codigo' => '22201',
                'valores' => 'fabricación de envases plásticos',
            ),
            199 => 
            array (
                'codigo' => '22202',
                'valores' => 'fabricación de productos plásticos para uso personal o doméstico',
            ),
            200 => 
            array (
                'codigo' => '22208',
                'valores' => 'maquila de plásticos',
            ),
            201 => 
            array (
                'codigo' => '22209',
                'valores' => 'fabricación de productos plásticos n.c.p.',
            ),
            202 => 
            array (
                'codigo' => '23101',
                'valores' => 'fabricación de vidrio',
            ),
            203 => 
            array (
                'codigo' => '23102',
                'valores' => 'fabricación de recipientes y envases de vidrio',
            ),
            204 => 
            array (
                'codigo' => '23108',
                'valores' => 'servicio de maquilado',
            ),
            205 => 
            array (
                'codigo' => '23109',
                'valores' => 'fabricación de productos de vidrio ncp',
            ),
            206 => 
            array (
                'codigo' => '23910',
                'valores' => 'fabricación de productos refractarios',
            ),
            207 => 
            array (
                'codigo' => '23920',
                'valores' => 'fabricación de productos de arcilla para la construcción',
            ),
            208 => 
            array (
                'codigo' => '23931',
                'valores' => 'fabricación de productos de cerámica y porcelana no refractaria',
            ),
            209 => 
            array (
                'codigo' => '23932',
                'valores' => 'fabricación de productos de cerámica y porcelana ncp',
            ),
            210 => 
            array (
                'codigo' => '23940',
                'valores' => 'fabricación de cemento, cal y yeso',
            ),
            211 => 
            array (
                'codigo' => '23950',
                'valores' => 'fabricación de artículos de hormigón, cemento y yeso',
            ),
            212 => 
            array (
                'codigo' => '23960',
                'valores' => 'corte, tallado y acabado de la piedra',
            ),
            213 => 
            array (
                'codigo' => '23990',
                'valores' => 'fabricación de productos minerales no metálicos ncp',
            ),
            214 => 
            array (
                'codigo' => '24100',
                'valores' => 'industrias básicas de hierro y acero',
            ),
            215 => 
            array (
                'codigo' => '24200',
                'valores' => 'fabricación de productos primarios de metales preciosos y metales no ferrosos',
            ),
            216 => 
            array (
                'codigo' => '24310',
                'valores' => 'fundición de hierro y acero',
            ),
            217 => 
            array (
                'codigo' => '24320',
                'valores' => 'fundición de metales no ferrosos',
            ),
            218 => 
            array (
                'codigo' => '25111',
                'valores' => 'fabricación de productos metálicos para uso estructural',
            ),
            219 => 
            array (
                'codigo' => '25118',
                'valores' => 'servicio de maquila para la fabricación de estructuras metálicas',
            ),
            220 => 
            array (
                'codigo' => '25120',
                'valores' => 'fabricación de tanques, depósitos y recipientes de metal',
            ),
            221 => 
            array (
                'codigo' => '25130',
                'valores' => 'fabricación de generadores de vapor, excepto calderas de agua caliente para calefacción central',
            ),
            222 => 
            array (
                'codigo' => '25200',
                'valores' => 'fabricación de armas y municiones',
            ),
            223 => 
            array (
                'codigo' => '25910',
                'valores' => 'forjado, prensado, estampado y laminado de metales; pulvimetalurgia',
            ),
            224 => 
            array (
                'codigo' => '25920',
                'valores' => 'tratamiento y revestimiento de metales',
            ),
            225 => 
            array (
                'codigo' => '25930',
                'valores' => 'fabricación de artículos de cuchillería, herramientas de mano y artículos de ferretería',
            ),
            226 => 
            array (
                'codigo' => '25991',
                'valores' => 'fabricación de envases y artículos conexos de metal',
            ),
            227 => 
            array (
                'codigo' => '25992',
                'valores' => 'fabricación de artículos metálicos de uso personal y/o doméstico',
            ),
            228 => 
            array (
                'codigo' => '25999',
                'valores' => 'fabricación de productos elaborados de metal ncp',
            ),
            229 => 
            array (
                'codigo' => '26100',
                'valores' => 'fabricación de componentes electrónicos',
            ),
            230 => 
            array (
                'codigo' => '26200',
                'valores' => 'fabricación de computadoras y equipo conexo',
            ),
            231 => 
            array (
                'codigo' => '26300',
                'valores' => 'fabricación de equipo de comunicaciones',
            ),
            232 => 
            array (
                'codigo' => '26400',
                'valores' => 'fabricación de aparatos electrónicos de consumo para audio, video, radio y televisión',
            ),
            233 => 
            array (
                'codigo' => '26510',
                'valores' => 'fabricación de instrumentos y aparatos para medir, verificar, ensayar, navegar y de control de procesos industriales',
            ),
            234 => 
            array (
                'codigo' => '26520',
                'valores' => 'fabricación de relojes y piezas de relojes',
            ),
            235 => 
            array (
                'codigo' => '26600',
                'valores' => 'fabricación de equipo médico de irradiación y equipo electrónico de uso médico y terapéutico',
            ),
            236 => 
            array (
                'codigo' => '26700',
                'valores' => 'fabricación de instrumentos de óptica y equipo fotográfico',
            ),
            237 => 
            array (
                'codigo' => '26800',
                'valores' => 'fabricación de medios magnéticos y ópticos',
            ),
            238 => 
            array (
                'codigo' => '27100',
                'valores' => 'fabricación de motores, generadores, transformadores eléctricos, aparatos de distribución y control de electricidad',
            ),
            239 => 
            array (
                'codigo' => '27200',
                'valores' => 'fabricación de pilas, baterías y acumuladores',
            ),
            240 => 
            array (
                'codigo' => '27310',
                'valores' => 'fabricación de cables de fibra óptica',
            ),
            241 => 
            array (
                'codigo' => '27320',
                'valores' => 'fabricación de otros hilos y cables eléctricos',
            ),
            242 => 
            array (
                'codigo' => '27330',
                'valores' => 'fabricación de dispositivos de cableados',
            ),
            243 => 
            array (
                'codigo' => '27400',
                'valores' => 'fabricación de equipo eléctrico de iluminación',
            ),
            244 => 
            array (
                'codigo' => '27500',
                'valores' => 'fabricación de aparatos de uso doméstico',
            ),
            245 => 
            array (
                'codigo' => '27900',
                'valores' => 'fabricación de otros tipos de equipo eléctrico',
            ),
            246 => 
            array (
                'codigo' => '28110',
                'valores' => 'fabricación de motores y turbinas, excepto motores para aeronaves, vehículos automotores y motocicletas',
            ),
            247 => 
            array (
                'codigo' => '28120',
                'valores' => 'fabricación de equipo hidráulico',
            ),
            248 => 
            array (
                'codigo' => '28130',
                'valores' => 'fabricación de otras bombas, compresores, grifos y válvulas',
            ),
            249 => 
            array (
                'codigo' => '28140',
                'valores' => 'fabricación de cojinetes, engranajes, trenes de engranajes y piezas de transmisión',
            ),
            250 => 
            array (
                'codigo' => '28150',
                'valores' => 'fabricación de hornos y quemadores',
            ),
            251 => 
            array (
                'codigo' => '28160',
                'valores' => 'fabricación de equipo de elevación y manipulación',
            ),
            252 => 
            array (
                'codigo' => '28170',
                'valores' => 'fabricación de maquinaria y equipo de oficina',
            ),
            253 => 
            array (
                'codigo' => '28180',
                'valores' => 'fabricación de herramientas manuales',
            ),
            254 => 
            array (
                'codigo' => '28190',
                'valores' => 'fabricación de otros tipos de maquinaria de uso general',
            ),
            255 => 
            array (
                'codigo' => '28210',
                'valores' => 'fabricación de maquinaria agropecuaria y forestal',
            ),
            256 => 
            array (
                'codigo' => '28220',
                'valores' => 'fabricación de máquinas para conformar metales y maquinaria herramienta',
            ),
            257 => 
            array (
                'codigo' => '28230',
                'valores' => 'fabricación de maquinaria metalúrgica',
            ),
            258 => 
            array (
                'codigo' => '28240',
                'valores' => 'fabricación de maquinaria para la explotación de minas y canteras y para obras de construcción',
            ),
            259 => 
            array (
                'codigo' => '28250',
                'valores' => 'fabricación de maquinaria para la elaboración de alimentos, bebidas y tabaco',
            ),
            260 => 
            array (
                'codigo' => '28260',
                'valores' => 'fabricación de maquinaria para la elaboración de productos textiles, prendas de vestir y cueros',
            ),
            261 => 
            array (
                'codigo' => '28291',
                'valores' => 'fabricación de máquinas para imprenta',
            ),
            262 => 
            array (
                'codigo' => '28299',
                'valores' => 'fabricación de maquinaria de uso especial ncp',
            ),
            263 => 
            array (
                'codigo' => '29100',
                'valores' => 'fabricación vehículos automotores',
            ),
            264 => 
            array (
                'codigo' => '29200',
                'valores' => 'fabricación de carrocerías para vehículos automotores; fabricación de remolques y semiremolques',
            ),
            265 => 
            array (
                'codigo' => '29300',
                'valores' => 'fabricación de partes, piezas y accesorios para vehículos automotores',
            ),
            266 => 
            array (
                'codigo' => '30110',
                'valores' => 'fabricación de buques',
            ),
            267 => 
            array (
                'codigo' => '30120',
                'valores' => 'construcción y reparación de embarcaciones de recreo',
            ),
            268 => 
            array (
                'codigo' => '30200',
                'valores' => 'fabricación de locomotoras y de material rodante',
            ),
            269 => 
            array (
                'codigo' => '30300',
                'valores' => 'fabricación de aeronaves y naves espaciales',
            ),
            270 => 
            array (
                'codigo' => '30400',
                'valores' => 'fabricación de vehículos militares de combate',
            ),
            271 => 
            array (
                'codigo' => '30910',
                'valores' => 'fabricación de motocicletas',
            ),
            272 => 
            array (
                'codigo' => '30920',
                'valores' => 'fabricación de bicicletas y sillones de ruedas para inválidos',
            ),
            273 => 
            array (
                'codigo' => '30990',
                'valores' => 'fabricación de equipo de transporte ncp',
            ),
            274 => 
            array (
                'codigo' => '31001',
                'valores' => 'fabricación de colchones y somier',
            ),
            275 => 
            array (
                'codigo' => '31002',
                'valores' => 'fabricación de muebles y otros productos de madera a medida',
            ),
            276 => 
            array (
                'codigo' => '31008',
                'valores' => 'servicios de maquilado de muebles',
            ),
            277 => 
            array (
                'codigo' => '31009',
                'valores' => 'fabricación de muebles ncp',
            ),
            278 => 
            array (
                'codigo' => '32110',
                'valores' => 'fabricación de joyas platerías y joyerías',
            ),
            279 => 
            array (
                'codigo' => '32120',
            'valores' => 'fabricación de joyas de imitación (fantasía) y artículos conexos',
            ),
            280 => 
            array (
                'codigo' => '32200',
                'valores' => 'fabricación de instrumentos musicales',
            ),
            281 => 
            array (
                'codigo' => '32301',
                'valores' => 'fabricación de artículos de deporte',
            ),
            282 => 
            array (
                'codigo' => '32308',
                'valores' => 'servicio de maquila de productos deportivos',
            ),
            283 => 
            array (
                'codigo' => '32401',
                'valores' => 'fabricación de juegos de mesa y de salón',
            ),
            284 => 
            array (
                'codigo' => '32402',
                'valores' => 'servicio de maquilado de juguetes y juegos',
            ),
            285 => 
            array (
                'codigo' => '32409',
                'valores' => 'fabricación de juegos y juguetes n.c.p.',
            ),
            286 => 
            array (
                'codigo' => '32500',
                'valores' => 'fabricación de instrumentos y materiales médicos y odontológicos',
            ),
            287 => 
            array (
                'codigo' => '32901',
                'valores' => 'fabricación de lápices, bolígrafos, sellos y artículos de librería en general',
            ),
            288 => 
            array (
                'codigo' => '32902',
                'valores' => 'fabricación de escobas, cepillos, pinceles y similares',
            ),
            289 => 
            array (
                'codigo' => '32903',
                'valores' => 'fabricación de artesanías de materiales diversos',
            ),
            290 => 
            array (
                'codigo' => '32904',
                'valores' => 'fabricación de artículos de uso personal y domésticos n.c.p.',
            ),
            291 => 
            array (
                'codigo' => '32905',
                'valores' => 'fabricación de accesorios para las confecciones y la marroquinería n.c.p.',
            ),
            292 => 
            array (
                'codigo' => '32908',
                'valores' => 'servicios de maquila ncp',
            ),
            293 => 
            array (
                'codigo' => '32909',
                'valores' => 'fabricación de productos manufacturados n.c.p.',
            ),
            294 => 
            array (
                'codigo' => '33110',
                'valores' => 'reparación y mantenimiento de productos elaborados de metal',
            ),
            295 => 
            array (
                'codigo' => '33120',
                'valores' => 'reparación y mantenimiento de maquinaria',
            ),
            296 => 
            array (
                'codigo' => '33130',
                'valores' => 'reparación y mantenimiento de equipo electrónico y óptico',
            ),
            297 => 
            array (
                'codigo' => '33140',
                'valores' => 'reparación y mantenimiento de equipo eléctrico',
            ),
            298 => 
            array (
                'codigo' => '33150',
                'valores' => 'reparación y mantenimiento de equipo de transporte, excepto vehículos automotores',
            ),
            299 => 
            array (
                'codigo' => '33190',
                'valores' => 'reparación y mantenimiento de equipos n.c.p.',
            ),
            300 => 
            array (
                'codigo' => '33200',
                'valores' => 'instalación de maquinaria y equipo industrial',
            ),
            301 => 
            array (
                'codigo' => '35101',
                'valores' => 'generación de energía eléctrica',
            ),
            302 => 
            array (
                'codigo' => '35102',
                'valores' => 'transmisión de energía eléctrica',
            ),
            303 => 
            array (
                'codigo' => '35103',
                'valores' => 'distribución de energía eléctrica',
            ),
            304 => 
            array (
                'codigo' => '35200',
                'valores' => 'fabricación de gas, distribución de combustibles gaseosos por tuberías',
            ),
            305 => 
            array (
                'codigo' => '35300',
                'valores' => 'suministro de vapor y agua caliente',
            ),
            306 => 
            array (
                'codigo' => '36000',
                'valores' => 'captacion, tratamiento y suministro de agua',
            ),
            307 => 
            array (
                'codigo' => '37000',
            'valores' => 'evacuacion de aguas residuales (alcantarillado)',
            ),
            308 => 
            array (
                'codigo' => '38110',
                'valores' => 'recoleccion y transporte de desechos solidos proveniente de hogares y sector urbano',
            ),
            309 => 
            array (
                'codigo' => '38120',
                'valores' => 'recoleccion de desechos peligrosos',
            ),
            310 => 
            array (
                'codigo' => '38210',
                'valores' => 'tratamiento y eliminacion de desechos inicuos',
            ),
            311 => 
            array (
                'codigo' => '38220',
                'valores' => 'tratamiento y eliminacion de desechos peligrosos',
            ),
            312 => 
            array (
                'codigo' => '38301',
                'valores' => 'reciclaje de desperdicios y desechos textiles',
            ),
            313 => 
            array (
                'codigo' => '38302',
                'valores' => 'reciclaje de desperdicios y desechos de plastico y caucho',
            ),
            314 => 
            array (
                'codigo' => '38303',
                'valores' => 'reciclaje de desperdicios y desechos de vidrio',
            ),
            315 => 
            array (
                'codigo' => '38304',
                'valores' => 'reciclaje de desperdicios y desechos de papel y carton',
            ),
            316 => 
            array (
                'codigo' => '38305',
                'valores' => 'reciclaje de desperdicios y desechos metalicos',
            ),
            317 => 
            array (
                'codigo' => '38309',
                'valores' => 'reciclaje de desperdicios y desechos no metalicos n.c.p.',
            ),
            318 => 
            array (
                'codigo' => '39000',
                'valores' => 'actividades de saneamiento y otros servicios de gestion de desechos',
            ),
            319 => 
            array (
                'codigo' => '41001',
                'valores' => 'construccion de edificios residenciales',
            ),
            320 => 
            array (
                'codigo' => '41002',
                'valores' => 'construccion de edificios no residenciales',
            ),
            321 => 
            array (
                'codigo' => '42100',
                'valores' => 'construccion de carreteras, calles y caminos',
            ),
            322 => 
            array (
                'codigo' => '42200',
                'valores' => 'construccion de proyectos de servicio publico',
            ),
            323 => 
            array (
                'codigo' => '42900',
                'valores' => 'construccion de obras de ingenieria civil n.c.p.',
            ),
            324 => 
            array (
                'codigo' => '43110',
                'valores' => 'demolicion',
            ),
            325 => 
            array (
                'codigo' => '43120',
                'valores' => 'preparacion de terreno',
            ),
            326 => 
            array (
                'codigo' => '43210',
                'valores' => 'instalaciones electricas',
            ),
            327 => 
            array (
                'codigo' => '43220',
                'valores' => 'instalacion de fontaneria, calefaccion y aire acondicionado',
            ),
            328 => 
            array (
                'codigo' => '43290',
                'valores' => 'otras instalaciones para obras de construccion',
            ),
            329 => 
            array (
                'codigo' => '43300',
                'valores' => 'terminacion y acabado de edificios',
            ),
            330 => 
            array (
                'codigo' => '43900',
                'valores' => 'otras actividades especializadas de construccion',
            ),
            331 => 
            array (
                'codigo' => '43901',
                'valores' => 'fabricacion de techos y materiales diversos',
            ),
            332 => 
            array (
                'codigo' => '45100',
                'valores' => 'venta de vehiculos automotores',
            ),
            333 => 
            array (
                'codigo' => '45201',
                'valores' => 'reparacion mecanica de vehiculos automotores',
            ),
            334 => 
            array (
                'codigo' => '45202',
                'valores' => 'reparaciones electricas del automotor y recarga de baterias',
            ),
            335 => 
            array (
                'codigo' => '45203',
                'valores' => 'enderezado y pintura de vehiculos automotores',
            ),
            336 => 
            array (
                'codigo' => '45204',
                'valores' => 'reparaciones de radiadores, escapes y silenciadores',
            ),
            337 => 
            array (
                'codigo' => '45205',
                'valores' => 'reparacion y reconstruccion de vias, stop y otros articulos de fibra de vidrio',
            ),
            338 => 
            array (
                'codigo' => '45206',
                'valores' => 'reparacion de llantas de vehiculos automotores',
            ),
            339 => 
            array (
                'codigo' => '45207',
            'valores' => 'polarizado de vehiculos (mediante la adhesion de papel especial a los vidrios)',
            ),
            340 => 
            array (
                'codigo' => '45208',
            'valores' => 'lavado y pasteado de vehiculos (carwash)',
            ),
            341 => 
            array (
                'codigo' => '45209',
                'valores' => 'reparaciones de vehiculos n.c.p.',
            ),
            342 => 
            array (
                'codigo' => '45211',
                'valores' => 'remolque de vehiculos automotores',
            ),
            343 => 
            array (
                'codigo' => '45301',
                'valores' => 'venta de partes, piezas y accesorios nuevos para vehiculos automotores',
            ),
            344 => 
            array (
                'codigo' => '45302',
                'valores' => 'venta de partes, piezas y accesorios usados para vehiculos automotores',
            ),
            345 => 
            array (
                'codigo' => '45401',
                'valores' => 'venta de motocicletas',
            ),
            346 => 
            array (
                'codigo' => '45402',
                'valores' => 'venta de repuestos, piezas y accesorios de motocicletas',
            ),
            347 => 
            array (
                'codigo' => '45403',
                'valores' => 'mantenimiento y reparacion de motocicletas',
            ),
            348 => 
            array (
                'codigo' => '46100',
                'valores' => 'venta al por mayor a cambio de retribucion o por contrata',
            ),
            349 => 
            array (
                'codigo' => '46201',
                'valores' => 'venta al por mayor de materias primas agricolas',
            ),
            350 => 
            array (
                'codigo' => '46202',
                'valores' => 'venta al por mayor de productos de la silvicultura',
            ),
            351 => 
            array (
                'codigo' => '46203',
                'valores' => 'venta al por mayor de productos pecuarios y de granja',
            ),
            352 => 
            array (
                'codigo' => '46211',
                'valores' => 'venta de productos para uso agropecuario',
            ),
            353 => 
            array (
                'codigo' => '46291',
            'valores' => 'venta al por mayor de granos basicos (cereales, leguminosas)',
            ),
            354 => 
            array (
                'codigo' => '46292',
                'valores' => 'venta al por mayor de semillas mejoradas para cultivo',
            ),
            355 => 
            array (
                'codigo' => '46293',
                'valores' => 'venta al por mayor de cafe oro y uva',
            ),
            356 => 
            array (
                'codigo' => '46294',
                'valores' => 'venta al por mayor de cana de azucar',
            ),
            357 => 
            array (
                'codigo' => '46295',
                'valores' => 'venta al por mayor de flores, plantas y otros productos naturales',
            ),
            358 => 
            array (
                'codigo' => '46296',
                'valores' => 'venta al por mayor de productos agricolas',
            ),
            359 => 
            array (
                'codigo' => '46297',
            'valores' => 'venta al por mayor de ganado bovino (vivo)',
            ),
            360 => 
            array (
                'codigo' => '46298',
                'valores' => 'venta al por mayor de animales porcinos, ovinos, caprino, caniculas, apicolas, avicolas vivos',
            ),
            361 => 
            array (
                'codigo' => '46299',
                'valores' => 'venta de otras especies vivas del reino animal',
            ),
            362 => 
            array (
                'codigo' => '46301',
                'valores' => 'venta al por mayor de alimentos',
            ),
            363 => 
            array (
                'codigo' => '46302',
                'valores' => 'venta al por mayor de bebidas',
            ),
            364 => 
            array (
                'codigo' => '46303',
                'valores' => 'venta al por mayor de tabaco',
            ),
            365 => 
            array (
                'codigo' => '46371',
            'valores' => 'venta al por mayor de frutas, hortalizas (verduras), legumbres y tuberculos',
            ),
            366 => 
            array (
                'codigo' => '46372',
                'valores' => 'venta al por mayor de pollos, gallinas destazadas, pavos y otras aves',
            ),
            367 => 
            array (
                'codigo' => '46373',
                'valores' => 'venta al por mayor de carne bovina y porcina, productos de carne y embutidos',
            ),
            368 => 
            array (
                'codigo' => '46374',
                'valores' => 'venta al por mayor de huevos',
            ),
            369 => 
            array (
                'codigo' => '46375',
                'valores' => 'venta al por mayor de productos lacteos',
            ),
            370 => 
            array (
                'codigo' => '46376',
            'valores' => 'venta al por mayor de productos farinaceos de panaderia (pan dulce, cakes, reposteria, etc.)',
            ),
            371 => 
            array (
                'codigo' => '46377',
                'valores' => 'venta al por mayor de pastas alimenticias, aceites y grasas comestibles vegetal y animal',
            ),
            372 => 
            array (
                'codigo' => '46378',
                'valores' => 'venta al por mayor de sal comestible',
            ),
            373 => 
            array (
                'codigo' => '46379',
                'valores' => 'venta al por mayor de azucar',
            ),
            374 => 
            array (
                'codigo' => '46391',
            'valores' => 'venta al por mayor de abarrotes (vinos, licores, productos alimenticios envasados, etc.)',
            ),
            375 => 
            array (
                'codigo' => '46392',
                'valores' => 'venta al por mayor de aguas gaseosas',
            ),
            376 => 
            array (
                'codigo' => '46393',
                'valores' => 'venta al por mayor de agua purificada',
            ),
            377 => 
            array (
                'codigo' => '46394',
                'valores' => 'venta al por mayor de refrescos y otras bebidas, liquidas o en polvo',
            ),
            378 => 
            array (
                'codigo' => '46395',
                'valores' => 'venta al por mayor de cerveza y licores',
            ),
            379 => 
            array (
                'codigo' => '46396',
                'valores' => 'venta al por mayor de hielo',
            ),
            380 => 
            array (
                'codigo' => '46411',
                'valores' => 'venta al por mayor de hilados, tejidos y productos textiles de merceria',
            ),
            381 => 
            array (
                'codigo' => '46412',
                'valores' => 'venta al por mayor de articulos textiles excepto confecciones para el hogar',
            ),
            382 => 
            array (
                'codigo' => '46413',
                'valores' => 'venta al por mayor de confecciones textiles para el hogar',
            ),
            383 => 
            array (
                'codigo' => '46414',
                'valores' => 'venta al por mayor de prendas de vestir y accesorios de vestir',
            ),
            384 => 
            array (
                'codigo' => '46415',
                'valores' => 'venta al por mayor de ropa usada',
            ),
            385 => 
            array (
                'codigo' => '46416',
                'valores' => 'venta al por mayor de calzado',
            ),
            386 => 
            array (
                'codigo' => '46417',
                'valores' => 'venta al por mayor de articulos de marroquineria y talabarteria',
            ),
            387 => 
            array (
                'codigo' => '46418',
                'valores' => 'venta al por mayor de articulos de peleteria',
            ),
            388 => 
            array (
                'codigo' => '46419',
                'valores' => 'venta al por mayor de otros articulos textiles n.c.p.',
            ),
            389 => 
            array (
                'codigo' => '46471',
                'valores' => 'venta al por mayor de instrumentos musicales',
            ),
            390 => 
            array (
                'codigo' => '46472',
                'valores' => 'venta al por mayor de colchones, almohadas, cojines, etc.',
            ),
            391 => 
            array (
                'codigo' => '46473',
                'valores' => 'venta al por mayor de articulos de aluminio para el hogar y para otros usos',
            ),
            392 => 
            array (
                'codigo' => '46474',
                'valores' => 'venta al por mayor de depositos y otros articulos plasticos para el hogar y otros usos, incluyendo los desechables de durapax y no desechables',
            ),
            393 => 
            array (
                'codigo' => '46475',
                'valores' => 'venta al por mayor de camaras fotograficas, accesorios y materiales',
            ),
            394 => 
            array (
                'codigo' => '46482',
                'valores' => 'venta al por mayor de medicamentos, articulos y otros productos de uso veterinario',
            ),
            395 => 
            array (
                'codigo' => '46483',
                'valores' => 'venta al por mayor de productos y articulos de belleza y de uso personal',
            ),
            396 => 
            array (
                'codigo' => '46484',
                'valores' => 'venta de productos farmaceuticos y medicinales',
            ),
            397 => 
            array (
                'codigo' => '46491',
                'valores' => 'venta al por mayor de productos medicinales, cosmeticos, perfumeria y productos de limpieza',
            ),
            398 => 
            array (
                'codigo' => '46492',
                'valores' => 'venta al por mayor de relojes y articulos de joyeria',
            ),
            399 => 
            array (
                'codigo' => '46493',
                'valores' => 'venta al por mayor de electrodomesticos y articulos del hogar excepto bazar; articulos de iluminacion',
            ),
            400 => 
            array (
                'codigo' => '46494',
                'valores' => 'venta al por mayor de articulos de bazar y similares',
            ),
            401 => 
            array (
                'codigo' => '46495',
                'valores' => 'venta al por mayor de articulos de optica',
            ),
            402 => 
            array (
                'codigo' => '46496',
                'valores' => 'venta al por mayor de revistas, periodicos, libros, articulos de libreria y articulos de papel y carton en general',
            ),
            403 => 
            array (
                'codigo' => '46497',
                'valores' => 'venta de articulos deportivos, juguetes y rodados',
            ),
            404 => 
            array (
                'codigo' => '46498',
                'valores' => 'venta al por mayor de productos usados para el hogar o el uso personal',
            ),
            405 => 
            array (
                'codigo' => '46499',
                'valores' => 'venta al por mayor de enseres domesticos y de uso personal n.c.p.',
            ),
            406 => 
            array (
                'codigo' => '46500',
                'valores' => 'venta al por mayor de bicicletas, partes, accesorios y otros',
            ),
            407 => 
            array (
                'codigo' => '46510',
                'valores' => 'venta al por mayor de computadoras, equipo periferico y programas informaticos',
            ),
            408 => 
            array (
                'codigo' => '46520',
                'valores' => 'venta al por mayor de equipos de comunicacion',
            ),
            409 => 
            array (
                'codigo' => '46530',
                'valores' => 'venta al por mayor de maquinaria y equipo agropecuario, accesorios, partes y suministros',
            ),
            410 => 
            array (
                'codigo' => '46590',
                'valores' => 'venta de equipos e instrumentos de uso profesional y cientifico y aparatos de medida y control',
            ),
            411 => 
            array (
                'codigo' => '46591',
                'valores' => 'venta al por mayor de maquinaria equipo, accesorios y materiales para la industria de la madera y sus productos',
            ),
            412 => 
            array (
                'codigo' => '46592',
                'valores' => 'venta al por mayor de maquinaria, equipo, accesorios y materiales para la industria grafica y del papel, carton y productos de papel y carton',
            ),
            413 => 
            array (
                'codigo' => '46593',
                'valores' => 'venta al por mayor de maquinaria, equipo, accesorios y materiales para la industria de productos quimicos, plastico y caucho',
            ),
            414 => 
            array (
                'codigo' => '46594',
                'valores' => 'venta al por mayor de maquinaria, equipo, accesorios y materiales para la industria metalica y de sus productos',
            ),
            415 => 
            array (
                'codigo' => '46595',
                'valores' => 'venta al por mayor de equipamiento para uso medico, odontologico, veterinario y servicios conexos',
            ),
            416 => 
            array (
                'codigo' => '46596',
                'valores' => 'venta al por mayor de maquinaria, equipo, accesorios y partes para la industria de la alimentacion',
            ),
            417 => 
            array (
                'codigo' => '46597',
                'valores' => 'venta al por mayor de maquinaria, equipo, accesorios y partes para la industria textil, confecciones y cuero',
            ),
            418 => 
            array (
                'codigo' => '46598',
                'valores' => 'venta al por mayor de maquinaria, equipo y accesorios para la construccion y explotacion de minas y canteras',
            ),
            419 => 
            array (
                'codigo' => '46599',
                'valores' => 'venta al por mayor de otro tipo de maquinaria y equipo con sus accesorios y partes',
            ),
            420 => 
            array (
                'codigo' => '46610',
                'valores' => 'venta al por mayor de otros combustibles solidos, liquidos, gaseosos y de productos conexos',
            ),
            421 => 
            array (
                'codigo' => '46612',
                'valores' => 'venta al por mayor de combustibles para automotores, aviones, barcos, maquinaria y otros',
            ),
            422 => 
            array (
                'codigo' => '46613',
                'valores' => 'venta al por mayor de lubricantes, grasas y otros aceites para automotores, maquinaria industrial, etc.',
            ),
            423 => 
            array (
                'codigo' => '46614',
                'valores' => 'venta al por mayor de gas propano',
            ),
            424 => 
            array (
                'codigo' => '46615',
                'valores' => 'venta al por mayor de lena y carbon',
            ),
            425 => 
            array (
                'codigo' => '46620',
                'valores' => 'venta al por mayor de metales y minerales metaliferos',
            ),
            426 => 
            array (
                'codigo' => '46631',
                'valores' => 'venta al por mayor de puertas, ventanas, vitrinas y similares',
            ),
            427 => 
            array (
                'codigo' => '46632',
                'valores' => 'venta al por mayor de articulos de ferreteria y pinturerias',
            ),
            428 => 
            array (
                'codigo' => '46633',
                'valores' => 'vidrierias',
            ),
            429 => 
            array (
                'codigo' => '46634',
                'valores' => 'venta al por mayor de maderas',
            ),
            430 => 
            array (
                'codigo' => '46639',
                'valores' => 'venta al por mayor de materiales para la construccion n.c.p.',
            ),
            431 => 
            array (
                'codigo' => '46691',
                'valores' => 'venta al por mayor de sal industrial sin yodar',
            ),
            432 => 
            array (
                'codigo' => '46692',
                'valores' => 'venta al por mayor de productos intermedios y desechos de origen textil',
            ),
            433 => 
            array (
                'codigo' => '46693',
                'valores' => 'venta al por mayor de productos intermedios y desechos de origen metalico',
            ),
            434 => 
            array (
                'codigo' => '46694',
                'valores' => 'venta al por mayor de productos intermedios y desechos de papel y carton',
            ),
            435 => 
            array (
                'codigo' => '46695',
                'valores' => 'venta al por mayor fertilizantes, abonos, agroquimicos y productos similares',
            ),
            436 => 
            array (
                'codigo' => '46696',
                'valores' => 'venta al por mayor de productos intermedios y desechos de origen plastico',
            ),
            437 => 
            array (
                'codigo' => '46697',
                'valores' => 'venta al por mayor de tintas para imprenta, productos curtientes y materias y productos colorantes',
            ),
            438 => 
            array (
                'codigo' => '46698',
                'valores' => 'venta de productos intermedios y desechos de origen quimico y de caucho',
            ),
            439 => 
            array (
                'codigo' => '46699',
                'valores' => 'venta al por mayor de productos intermedios y desechos ncp',
            ),
            440 => 
            array (
                'codigo' => '46701',
                'valores' => 'venta de algodon en oro',
            ),
            441 => 
            array (
                'codigo' => '46900',
                'valores' => 'venta al por mayor de otros productos',
            ),
            442 => 
            array (
                'codigo' => '46901',
                'valores' => 'venta al por mayor de cohetes y otros productos pirotecnicos',
            ),
            443 => 
            array (
                'codigo' => '46902',
                'valores' => 'venta al por mayor de articulos diversos para consumo humano',
            ),
            444 => 
            array (
                'codigo' => '46903',
                'valores' => 'venta al por mayor de armas de fuego, municiones y accesorios',
            ),
            445 => 
            array (
                'codigo' => '46904',
                'valores' => 'venta al por mayor de toldos y tiendas de campana de cualquier material',
            ),
            446 => 
            array (
                'codigo' => '46905',
                'valores' => 'venta al por mayor de exhibidores publicitarios y rotulos',
            ),
            447 => 
            array (
                'codigo' => '46906',
                'valores' => 'venta al por mayor de articulos promocionales diversos',
            ),
            448 => 
            array (
                'codigo' => '47111',
                'valores' => 'venta en supermercados',
            ),
            449 => 
            array (
                'codigo' => '47112',
                'valores' => 'venta en tiendas de articulos de primera necesidad',
            ),
            450 => 
            array (
                'codigo' => '47119',
            'valores' => 'almacenes (venta de diversos articulos)',
            ),
            451 => 
            array (
                'codigo' => '47190',
                'valores' => 'venta al por menor de otros productos en comercios no especializados',
            ),
            452 => 
            array (
                'codigo' => '47199',
                'valores' => 'venta de establecimientos no especializados con surtido compuesto principalmente de alimentos, bebidas y tabaco',
            ),
            453 => 
            array (
                'codigo' => '47211',
                'valores' => 'venta al por menor de frutas y hortalizas',
            ),
            454 => 
            array (
                'codigo' => '47212',
                'valores' => 'venta al por menor de carnes, embutidos y productos de granja',
            ),
            455 => 
            array (
                'codigo' => '47213',
                'valores' => 'venta al por menor de pescado y mariscos',
            ),
            456 => 
            array (
                'codigo' => '47214',
                'valores' => 'venta al por menor de productos lacteos',
            ),
            457 => 
            array (
                'codigo' => '47215',
                'valores' => 'venta al por menor de productos de panaderia, reposteria y galletas',
            ),
            458 => 
            array (
                'codigo' => '47216',
                'valores' => 'venta al por menor de huevos',
            ),
            459 => 
            array (
                'codigo' => '47217',
                'valores' => 'venta al por menor de carnes y productos carnicos',
            ),
            460 => 
            array (
                'codigo' => '47218',
                'valores' => 'venta al por menor de granos basicos y otros',
            ),
            461 => 
            array (
                'codigo' => '47219',
                'valores' => 'venta al por menor de alimentos n.c.p.',
            ),
            462 => 
            array (
                'codigo' => '47221',
                'valores' => 'venta al por menor de hielo',
            ),
            463 => 
            array (
                'codigo' => '47223',
                'valores' => 'venta de bebidas no alcoholicas, para su consumo fuera del establecimiento',
            ),
            464 => 
            array (
                'codigo' => '47224',
                'valores' => 'venta de bebidas alcoholicas, para su consumo fuera del establecimiento',
            ),
            465 => 
            array (
                'codigo' => '47225',
                'valores' => 'venta de bebidas alcoholicas para su consumo dentro del establecimiento',
            ),
            466 => 
            array (
                'codigo' => '47230',
                'valores' => 'venta al por menor de tabaco',
            ),
            467 => 
            array (
                'codigo' => '47300',
            'valores' => 'venta de combustibles, lubricantes y otros (gasolineras)',
            ),
            468 => 
            array (
                'codigo' => '47411',
                'valores' => 'venta al por menor de computadoras y equipo periferico',
            ),
            469 => 
            array (
                'codigo' => '47412',
                'valores' => 'venta de equipo y accesorios de telecomunicacion',
            ),
            470 => 
            array (
                'codigo' => '47420',
                'valores' => 'venta al por menor de equipo de audio y video',
            ),
            471 => 
            array (
                'codigo' => '47510',
                'valores' => 'venta al por menor de hilados, tejidos y productos textiles de merceria; confecciones para el hogar y textiles n.c.p.',
            ),
            472 => 
            array (
                'codigo' => '47521',
                'valores' => 'venta al por menor de productos de madera',
            ),
            473 => 
            array (
                'codigo' => '47522',
                'valores' => 'venta al por menor de articulos de ferreteria',
            ),
            474 => 
            array (
                'codigo' => '47523',
                'valores' => 'venta al por menor de productos de pinturerias',
            ),
            475 => 
            array (
                'codigo' => '47524',
                'valores' => 'venta al por menor en vidrierias',
            ),
            476 => 
            array (
                'codigo' => '47529',
                'valores' => 'venta al por menor de materiales de construccion y articulos conexos',
            ),
            477 => 
            array (
                'codigo' => '47530',
                'valores' => 'venta al por menor de tapices, alfombras y revestimientos de paredes y pisos en comercios especializados',
            ),
            478 => 
            array (
                'codigo' => '47591',
                'valores' => 'venta al por menor de muebles',
            ),
            479 => 
            array (
                'codigo' => '47592',
                'valores' => 'venta al por menor de articulos de bazar',
            ),
            480 => 
            array (
                'codigo' => '47593',
                'valores' => 'venta al por menor de aparatos electrodomesticos, repuestos y accesorios',
            ),
            481 => 
            array (
                'codigo' => '47594',
                'valores' => 'venta al por menor de articulos electricos y de iluminacion',
            ),
            482 => 
            array (
                'codigo' => '47598',
                'valores' => 'venta al por menor de instrumentos musicales',
            ),
            483 => 
            array (
                'codigo' => '47610',
                'valores' => 'venta al por menor de libros, periodicos y articulos de papeleria en comercios especializados',
            ),
            484 => 
            array (
                'codigo' => '47620',
                'valores' => 'venta al por menor de discos laser, cassettes, cintas de video y otros',
            ),
            485 => 
            array (
                'codigo' => '47630',
                'valores' => 'venta al por menor de productos y equipos de deporte',
            ),
            486 => 
            array (
                'codigo' => '47631',
                'valores' => 'venta al por menor de bicicletas, accesorios y repuestos',
            ),
            487 => 
            array (
                'codigo' => '47640',
                'valores' => 'venta al por menor de juegos y juguetes en comercios especializados',
            ),
            488 => 
            array (
                'codigo' => '47711',
                'valores' => 'venta al por menor de prendas de vestir y accesorios de vestir',
            ),
            489 => 
            array (
                'codigo' => '47712',
                'valores' => 'venta al por menor de calzado',
            ),
            490 => 
            array (
                'codigo' => '47713',
                'valores' => 'venta al por menor de articulos de peleteria, marroquineria y talabarteria',
            ),
            491 => 
            array (
                'codigo' => '47721',
                'valores' => 'venta al por menor de medicamentos farmaceuticos y otros materiales y articulos de uso medico, odontologico y veterinario',
            ),
            492 => 
            array (
                'codigo' => '47722',
                'valores' => 'venta al por menor de productos cosmeticos y de tocador',
            ),
            493 => 
            array (
                'codigo' => '47731',
                'valores' => 'venta al por menor de productos de joyeria, bisuteria, optica, relojeria',
            ),
            494 => 
            array (
                'codigo' => '47732',
                'valores' => 'venta al por menor de plantas, semillas, animales y articulos conexos',
            ),
            495 => 
            array (
                'codigo' => '47733',
            'valores' => 'venta al por menor de combustibles de uso domestico (gas propano y gas licuado)',
            ),
            496 => 
            array (
                'codigo' => '47734',
                'valores' => 'venta al por menor de artesanias, articulos ceramicos y recuerdos en general',
            ),
            497 => 
            array (
                'codigo' => '47735',
                'valores' => 'venta al por menor de ataudes, lapidas y cruces, trofeos, articulos religiosos en general',
            ),
            498 => 
            array (
                'codigo' => '47736',
                'valores' => 'venta al por menor de armas de fuego, municiones y accesorios',
            ),
            499 => 
            array (
                'codigo' => '47737',
                'valores' => 'venta al por menor de articulos de coheteria y pirotecnicos',
            ),
        ));
        \DB::table('cat_019')->insert(array (
            0 => 
            array (
                'codigo' => '47738',
            'valores' => 'venta al por menor de articulos desechables de uso personal y domestico (servilletas, papel higienico, panales, toallas sanitarias, etc.)',
            ),
            1 => 
            array (
                'codigo' => '47739',
                'valores' => 'venta al por menor de otros productos n.c.p.',
            ),
            2 => 
            array (
                'codigo' => '47741',
                'valores' => 'venta al por menor de articulos usados',
            ),
            3 => 
            array (
                'codigo' => '47742',
                'valores' => 'venta al por menor de textiles y confecciones usados',
            ),
            4 => 
            array (
                'codigo' => '47743',
                'valores' => 'venta al por menor de libros, revistas, papel y carton usados',
            ),
            5 => 
            array (
                'codigo' => '47749',
                'valores' => 'venta al por menor de productos usados n.c.p.',
            ),
            6 => 
            array (
                'codigo' => '47811',
                'valores' => 'venta al por menor de frutas, verduras y hortalizas',
            ),
            7 => 
            array (
                'codigo' => '47814',
                'valores' => 'venta al por menor de productos lacteos',
            ),
            8 => 
            array (
                'codigo' => '47815',
                'valores' => 'venta al por menor de productos de panaderia, galletas y similares',
            ),
            9 => 
            array (
                'codigo' => '47816',
                'valores' => 'venta al por menor de bebidas',
            ),
            10 => 
            array (
                'codigo' => '47818',
                'valores' => 'venta al por menor en tiendas de mercado y puestos',
            ),
            11 => 
            array (
                'codigo' => '47821',
                'valores' => 'venta al por menor de hilados, tejidos y productos textiles de merceria en puestos de mercados y ferias',
            ),
            12 => 
            array (
                'codigo' => '47822',
                'valores' => 'venta al por menor de articulos textiles excepto confecciones para el hogar en puestos de mercados y ferias',
            ),
            13 => 
            array (
                'codigo' => '47823',
                'valores' => 'venta al por menor de confecciones textiles para el hogar en puestos de mercados y ferias',
            ),
            14 => 
            array (
                'codigo' => '47824',
                'valores' => 'venta al por menor de prendas de vestir, accesorios de vestir y similares en puestos de mercados y ferias',
            ),
            15 => 
            array (
                'codigo' => '47825',
                'valores' => 'venta al por menor de ropa usada',
            ),
            16 => 
            array (
                'codigo' => '47826',
                'valores' => 'venta al por menor de calzado, articulos de marroquineria y talabarteria en puestos de mercados y ferias',
            ),
            17 => 
            array (
                'codigo' => '47827',
                'valores' => 'venta al por menor de articulos de marroquineria y talabarteria en puestos de mercados y ferias',
            ),
            18 => 
            array (
                'codigo' => '47829',
                'valores' => 'venta al por menor de articulos textiles ncp en puestos de mercados y ferias',
            ),
            19 => 
            array (
                'codigo' => '47891',
                'valores' => 'venta al por menor de animales, flores y productos conexos en puestos de feria y mercados',
            ),
            20 => 
            array (
                'codigo' => '47892',
                'valores' => 'venta al por menor de productos medicinales, cosmeticos, de tocador y de limpieza en puestos de ferias y mercados',
            ),
            21 => 
            array (
                'codigo' => '47893',
                'valores' => 'venta al por menor de articulos de bazar en puestos de ferias y mercados',
            ),
            22 => 
            array (
                'codigo' => '47894',
                'valores' => 'venta al por menor de articulos de papel, envases, libros, revistas y conexos en puestos de feria y mercados',
            ),
            23 => 
            array (
                'codigo' => '47895',
                'valores' => 'venta al por menor de materiales de construccion, electrodomesticos, accesorios para autos y similares en puestos de feria y mercados',
            ),
            24 => 
            array (
                'codigo' => '47896',
                'valores' => 'venta al por menor de equipos accesorios para las comunicaciones en puestos de feria y mercados',
            ),
            25 => 
            array (
                'codigo' => '47899',
                'valores' => 'venta al por menor en puestos de ferias y mercados n.c.p.',
            ),
            26 => 
            array (
                'codigo' => '47910',
                'valores' => 'venta al por menor por correo o internet',
            ),
            27 => 
            array (
                'codigo' => '47990',
                'valores' => 'otros tipos de venta al por menor no realizada, en almacenes, puestos de venta o mercado',
            ),
            28 => 
            array (
                'codigo' => '49110',
                'valores' => 'transporte interurbano de pasajeros por ferrocarril',
            ),
            29 => 
            array (
                'codigo' => '49120',
                'valores' => 'transporte de carga por ferrocarril',
            ),
            30 => 
            array (
                'codigo' => '49211',
                'valores' => 'transporte de pasajeros urbanos e interurbano mediante buses',
            ),
            31 => 
            array (
                'codigo' => '49212',
                'valores' => 'transporte de pasajeros interdepartamental mediante microbuses',
            ),
            32 => 
            array (
                'codigo' => '49213',
                'valores' => 'transporte de pasajeros urbanos e interurbano mediante microbuses',
            ),
            33 => 
            array (
                'codigo' => '49214',
                'valores' => 'transporte de pasajeros interdepartamental mediante buses',
            ),
            34 => 
            array (
                'codigo' => '49221',
                'valores' => 'transporte internacional de pasajeros',
            ),
            35 => 
            array (
                'codigo' => '49222',
                'valores' => 'transporte de pasajeros mediante taxis y autos con chofer',
            ),
            36 => 
            array (
                'codigo' => '49223',
                'valores' => 'transporte escolar',
            ),
            37 => 
            array (
                'codigo' => '49225',
                'valores' => 'transporte de pasajeros para excursiones',
            ),
            38 => 
            array (
                'codigo' => '49226',
                'valores' => 'servicios de transporte de personal',
            ),
            39 => 
            array (
                'codigo' => '49229',
                'valores' => 'transporte de pasajeros por via terrestre ncp',
            ),
            40 => 
            array (
                'codigo' => '49231',
                'valores' => 'transporte de carga urbano',
            ),
            41 => 
            array (
                'codigo' => '49232',
                'valores' => 'transporte nacional de carga',
            ),
            42 => 
            array (
                'codigo' => '49233',
                'valores' => 'transporte de carga internacional',
            ),
            43 => 
            array (
                'codigo' => '49234',
                'valores' => 'servicios de mudanza',
            ),
            44 => 
            array (
                'codigo' => '49235',
                'valores' => 'alquiler de vehiculos de carga con conductor',
            ),
            45 => 
            array (
                'codigo' => '49300',
                'valores' => 'transporte por oleoducto o gasoducto',
            ),
            46 => 
            array (
                'codigo' => '50110',
                'valores' => 'transporte de pasajeros maritimo y de cabotaje',
            ),
            47 => 
            array (
                'codigo' => '50120',
                'valores' => 'transporte de carga maritimo y de cabotaje',
            ),
            48 => 
            array (
                'codigo' => '50211',
                'valores' => 'transporte de pasajeros por vias de navegacion interiores',
            ),
            49 => 
            array (
                'codigo' => '50212',
                'valores' => 'alquiler de equipo de transporte de pasajeros por vias de navegacion interior con conductor',
            ),
            50 => 
            array (
                'codigo' => '50220',
                'valores' => 'transporte de carga por vias de navegacion interiores',
            ),
            51 => 
            array (
                'codigo' => '51100',
                'valores' => 'transporte aereo de pasajeros',
            ),
            52 => 
            array (
                'codigo' => '51201',
                'valores' => 'transporte de carga por via aerea',
            ),
            53 => 
            array (
                'codigo' => '51202',
                'valores' => 'alquiler de equipo de aerotransporte con operadores para el proposito de transportar carga',
            ),
            54 => 
            array (
                'codigo' => '52101',
                'valores' => 'alquiler de instalaciones de almacenamiento en zonas francas',
            ),
            55 => 
            array (
                'codigo' => '52102',
                'valores' => 'alquiler de silos para conservacion y almacenamiento de granos',
            ),
            56 => 
            array (
                'codigo' => '52103',
                'valores' => 'alquiler de instalaciones con refrigeracion para almacenamiento y conservacion de alimentos y otros productos',
            ),
            57 => 
            array (
                'codigo' => '52109',
                'valores' => 'alquiler de bodegas para almacenamiento y deposito n.c.p.',
            ),
            58 => 
            array (
                'codigo' => '52211',
                'valores' => 'servicio de garaje y estacionamiento',
            ),
            59 => 
            array (
                'codigo' => '52212',
                'valores' => 'servicios de terminales para el transporte por via terrestre',
            ),
            60 => 
            array (
                'codigo' => '52219',
                'valores' => 'servicios para el transporte por via terrestre n.c.p.',
            ),
            61 => 
            array (
                'codigo' => '52220',
                'valores' => 'servicios para el transporte acuatico',
            ),
            62 => 
            array (
                'codigo' => '52230',
                'valores' => 'servicios para el transporte aereo',
            ),
            63 => 
            array (
                'codigo' => '52240',
                'valores' => 'manipulacion de carga',
            ),
            64 => 
            array (
                'codigo' => '52290',
                'valores' => 'servicios para el transporte ncp',
            ),
            65 => 
            array (
                'codigo' => '52291',
                'valores' => 'agencias de tramitaciones aduanales',
            ),
            66 => 
            array (
                'codigo' => '53100',
                'valores' => 'servicios de correo nacional',
            ),
            67 => 
            array (
                'codigo' => '53200',
                'valores' => 'actividades de correo distintas a las actividades postales nacionales',
            ),
            68 => 
            array (
                'codigo' => '55101',
                'valores' => 'actividades de alojamiento para estancias cortas',
            ),
            69 => 
            array (
                'codigo' => '55102',
                'valores' => 'hoteles',
            ),
            70 => 
            array (
                'codigo' => '55200',
                'valores' => 'actividades de campamentos, parques de vehiculos de recreo y parques de caravanas',
            ),
            71 => 
            array (
                'codigo' => '55900',
                'valores' => 'alojamiento n.c.p.',
            ),
            72 => 
            array (
                'codigo' => '56101',
                'valores' => 'restaurantes',
            ),
            73 => 
            array (
                'codigo' => '56106',
                'valores' => 'pupuseria',
            ),
            74 => 
            array (
                'codigo' => '56107',
                'valores' => 'actividades varias de restaurantes',
            ),
            75 => 
            array (
                'codigo' => '56108',
                'valores' => 'comedores',
            ),
            76 => 
            array (
                'codigo' => '56109',
                'valores' => 'merenderos ambulantes',
            ),
            77 => 
            array (
                'codigo' => '56210',
                'valores' => 'preparacion de comida para eventos especiales',
            ),
            78 => 
            array (
                'codigo' => '56291',
                'valores' => 'servicios de provision de comidas por contrato',
            ),
            79 => 
            array (
                'codigo' => '56292',
                'valores' => 'servicios de concesion de cafetines y chalet en empresas e instituciones',
            ),
            80 => 
            array (
                'codigo' => '56299',
                'valores' => 'servicios de preparacion de comidas ncp',
            ),
            81 => 
            array (
                'codigo' => '56301',
                'valores' => 'servicio de expendio de bebidas en salones y bares',
            ),
            82 => 
            array (
                'codigo' => '56302',
                'valores' => 'servicio de expendio de bebidas en puestos callejeros, mercados y ferias',
            ),
            83 => 
            array (
                'codigo' => '58110',
                'valores' => 'edicion de libros, folletos, partituras y otras ediciones distintas a estas',
            ),
            84 => 
            array (
                'codigo' => '58120',
                'valores' => 'edicion de directorios y listas de correos',
            ),
            85 => 
            array (
                'codigo' => '58130',
                'valores' => 'edicion de periodicos, revistas y otras publicaciones periodicas',
            ),
            86 => 
            array (
                'codigo' => '58190',
                'valores' => 'otras actividades de edicion',
            ),
            87 => 
            array (
                'codigo' => '58200',
            'valores' => 'edicion de programas informaticos (software)',
            ),
            88 => 
            array (
                'codigo' => '59110',
                'valores' => 'actividades de produccion cinematografica',
            ),
            89 => 
            array (
                'codigo' => '59120',
                'valores' => 'actividades de post produccion de peliculas, videos y programas de television',
            ),
            90 => 
            array (
                'codigo' => '59130',
                'valores' => 'actividades de distribucion de peliculas cinematograficas, videos y programas de television',
            ),
            91 => 
            array (
                'codigo' => '59140',
                'valores' => 'actividades de exhibicion de peliculas cinematograficas y cintas de video',
            ),
            92 => 
            array (
                'codigo' => '59200',
                'valores' => 'actividades de edicion y grabacion de musica',
            ),
            93 => 
            array (
                'codigo' => '60100',
                'valores' => 'servicios de difusiones de radio',
            ),
            94 => 
            array (
                'codigo' => '60201',
                'valores' => 'actividades de programacion y difusion de television abierta',
            ),
            95 => 
            array (
                'codigo' => '60202',
                'valores' => 'actividades de suscripcion y difusion de television por cable y/o suscripcion',
            ),
            96 => 
            array (
                'codigo' => '60299',
                'valores' => 'servicios de television, incluye television por cable',
            ),
            97 => 
            array (
                'codigo' => '60900',
                'valores' => 'programacion y transmision de radio y television',
            ),
            98 => 
            array (
                'codigo' => '61101',
                'valores' => 'servicio de telefonia',
            ),
            99 => 
            array (
                'codigo' => '61102',
                'valores' => 'servicio de internet',
            ),
            100 => 
            array (
                'codigo' => '61103',
                'valores' => 'servicio de telefonia fija',
            ),
            101 => 
            array (
                'codigo' => '61109',
                'valores' => 'servicio de internet n.c.p.',
            ),
            102 => 
            array (
                'codigo' => '61201',
                'valores' => 'servicios de telefonia celular',
            ),
            103 => 
            array (
                'codigo' => '61202',
                'valores' => 'servicios de internet inalambrico',
            ),
            104 => 
            array (
                'codigo' => '61209',
                'valores' => 'servicios de telecomunicaciones inalambrico n.c.p.',
            ),
            105 => 
            array (
                'codigo' => '61301',
                'valores' => 'telecomunicaciones satelitales',
            ),
            106 => 
            array (
                'codigo' => '61309',
                'valores' => 'comunicacion via satelite n.c.p.',
            ),
            107 => 
            array (
                'codigo' => '61900',
                'valores' => 'actividades de telecomunicacion n.c.p.',
            ),
            108 => 
            array (
                'codigo' => '62010',
                'valores' => 'programacion informatica',
            ),
            109 => 
            array (
                'codigo' => '62020',
                'valores' => 'consultorias y gestion de servicios informaticos',
            ),
            110 => 
            array (
                'codigo' => '62090',
                'valores' => 'otras actividades de tecnologia de informacion y servicios de computadora',
            ),
            111 => 
            array (
                'codigo' => '63110',
                'valores' => 'procesamiento de datos y actividades relacionadas',
            ),
            112 => 
            array (
                'codigo' => '63120',
                'valores' => 'portales web',
            ),
            113 => 
            array (
                'codigo' => '63910',
                'valores' => 'servicios de agencias de noticias',
            ),
            114 => 
            array (
                'codigo' => '63990',
                'valores' => 'otros servicios de informacion n.c.p.',
            ),
            115 => 
            array (
                'codigo' => '64110',
                'valores' => 'servicios provistos por el banco central de el salvador',
            ),
            116 => 
            array (
                'codigo' => '64190',
                'valores' => 'bancos',
            ),
            117 => 
            array (
                'codigo' => '64192',
                'valores' => 'entidades dedicadas al envio de remesas',
            ),
            118 => 
            array (
                'codigo' => '64199',
                'valores' => 'otras entidades financieras',
            ),
            119 => 
            array (
                'codigo' => '64200',
                'valores' => 'actividades de sociedades de cartera',
            ),
            120 => 
            array (
                'codigo' => '64300',
                'valores' => 'fideicomisos, fondos y otras fuentes de financiamiento',
            ),
            121 => 
            array (
                'codigo' => '64910',
                'valores' => 'arrendamientos financieros',
            ),
            122 => 
            array (
                'codigo' => '64920',
                'valores' => 'asociaciones cooperativas de ahorro y credito dedicadas a la intermediacion financiera',
            ),
            123 => 
            array (
                'codigo' => '64921',
                'valores' => 'instituciones emisoras de tarjetas de credito y otros',
            ),
            124 => 
            array (
                'codigo' => '64922',
                'valores' => 'tipos de credito ncp',
            ),
            125 => 
            array (
                'codigo' => '64928',
                'valores' => 'prestamistas y casas de empeño',
            ),
            126 => 
            array (
                'codigo' => '64990',
                'valores' => 'actividades de servicios financieros, excepto la financiacion de planes de seguros y de pensiones n.c.p.',
            ),
            127 => 
            array (
                'codigo' => '65110',
                'valores' => 'planes de seguros de vida',
            ),
            128 => 
            array (
                'codigo' => '65120',
                'valores' => 'planes de seguro excepto de vida',
            ),
            129 => 
            array (
                'codigo' => '65199',
                'valores' => 'seguros generales de todo tipo',
            ),
            130 => 
            array (
                'codigo' => '65200',
                'valores' => 'planes se seguro',
            ),
            131 => 
            array (
                'codigo' => '65300',
                'valores' => 'planes de pensiones',
            ),
            132 => 
            array (
                'codigo' => '66110',
            'valores' => 'administracion de mercados financieros (bolsa de valores)',
            ),
            133 => 
            array (
                'codigo' => '66120',
            'valores' => 'actividades bursatiles (corredores de bolsa)',
            ),
            134 => 
            array (
                'codigo' => '66190',
                'valores' => 'actividades auxiliares de la intermediacion financiera ncp',
            ),
            135 => 
            array (
                'codigo' => '66210',
                'valores' => 'evaluacion de riesgos y danos',
            ),
            136 => 
            array (
                'codigo' => '66220',
                'valores' => 'actividades de agentes y corredores de seguros',
            ),
            137 => 
            array (
                'codigo' => '66290',
                'valores' => 'otras actividades auxiliares de seguros y fondos de pensiones',
            ),
            138 => 
            array (
                'codigo' => '66300',
                'valores' => 'actividades de administracion de fondos',
            ),
            139 => 
            array (
                'codigo' => '68101',
                'valores' => 'servicio de alquiler y venta de lotes en cementerios',
            ),
            140 => 
            array (
                'codigo' => '68109',
                'valores' => 'actividades inmobiliarias realizadas con bienes propios o arrendados n.c.p.',
            ),
            141 => 
            array (
                'codigo' => '68200',
                'valores' => 'actividades inmobiliarias realizadas a cambio de una retribucion o por contrata',
            ),
            142 => 
            array (
                'codigo' => '69100',
                'valores' => 'actividades juridicas',
            ),
            143 => 
            array (
                'codigo' => '69200',
                'valores' => 'actividades de contabilidad, teneduria de libros y auditoria; asesoramiento en materia de impuestos',
            ),
            144 => 
            array (
                'codigo' => '70100',
                'valores' => 'actividades de oficinas centrales de sociedades de cartera',
            ),
            145 => 
            array (
                'codigo' => '70200',
                'valores' => 'actividades de consultoria en gestion empresarial',
            ),
            146 => 
            array (
                'codigo' => '71101',
                'valores' => 'servicios de arquitectura y planificacion urbana y servicios conexos',
            ),
            147 => 
            array (
                'codigo' => '71102',
                'valores' => 'servicios de ingenieria',
            ),
            148 => 
            array (
                'codigo' => '71103',
                'valores' => 'servicios de agrimensura, topografia, cartografia, prospeccion y geofisica y servicios conexos',
            ),
            149 => 
            array (
                'codigo' => '71200',
                'valores' => 'ensayos y analisis tecnicos',
            ),
            150 => 
            array (
                'codigo' => '72100',
                'valores' => 'investigaciones y desarrollo experimental en el campo de las ciencias naturales y la ingenieria',
            ),
            151 => 
            array (
                'codigo' => '72199',
                'valores' => 'investigaciones cientificas',
            ),
            152 => 
            array (
                'codigo' => '72200',
                'valores' => 'investigaciones y desarrollo experimental en el campo de las ciencias sociales y las humanidades cientifica y desarrollo',
            ),
            153 => 
            array (
                'codigo' => '73100',
                'valores' => 'publicidad',
            ),
            154 => 
            array (
                'codigo' => '73200',
                'valores' => 'investigacion de mercados y realizacion de encuestas de opinion publica',
            ),
            155 => 
            array (
                'codigo' => '74100',
                'valores' => 'actividades de diseño especializado',
            ),
            156 => 
            array (
                'codigo' => '74200',
                'valores' => 'actividades de fotografia',
            ),
            157 => 
            array (
                'codigo' => '74900',
                'valores' => 'servicios profesionales y cientificos ncp',
            ),
            158 => 
            array (
                'codigo' => '75000',
                'valores' => 'actividades veterinarias',
            ),
            159 => 
            array (
                'codigo' => '77101',
                'valores' => 'alquiler de equipo de transporte terrestre',
            ),
            160 => 
            array (
                'codigo' => '77102',
                'valores' => 'alquiler de equipo de transporte acuatico',
            ),
            161 => 
            array (
                'codigo' => '77103',
                'valores' => 'alquiler de equipo de transporte por via aerea',
            ),
            162 => 
            array (
                'codigo' => '77210',
                'valores' => 'alquiler y arrendamiento de equipo de recreo y deportivo',
            ),
            163 => 
            array (
                'codigo' => '77220',
                'valores' => 'alquiler de cintas de video y discos',
            ),
            164 => 
            array (
                'codigo' => '77290',
                'valores' => 'alquiler de otros efectos personales y enseres domesticos',
            ),
            165 => 
            array (
                'codigo' => '77300',
                'valores' => 'alquiler de maquinaria y equipo',
            ),
            166 => 
            array (
                'codigo' => '77400',
                'valores' => 'arrendamiento de productos de propiedad intelectual',
            ),
            167 => 
            array (
                'codigo' => '78100',
                'valores' => 'obtencion y dotacion de personal',
            ),
            168 => 
            array (
                'codigo' => '78200',
                'valores' => 'actividades de las agencias de trabajo temporal',
            ),
            169 => 
            array (
                'codigo' => '78300',
                'valores' => 'dotacion de recursos humanos y gestion; gestion de las funciones de recursos humanos',
            ),
            170 => 
            array (
                'codigo' => '79110',
                'valores' => 'actividades de agencias de viajes y organizadores de viajes; actividades de asistencia a turistas',
            ),
            171 => 
            array (
                'codigo' => '79120',
                'valores' => 'actividades de los operadores turisticos',
            ),
            172 => 
            array (
                'codigo' => '79900',
                'valores' => 'otros servicios de reservas y actividades relacionadas',
            ),
            173 => 
            array (
                'codigo' => '80100',
                'valores' => 'servicios de seguridad privados',
            ),
            174 => 
            array (
                'codigo' => '80201',
                'valores' => 'actividades de servicios de sistemas de seguridad',
            ),
            175 => 
            array (
                'codigo' => '80202',
                'valores' => 'actividades para la prestacion de sistemas de seguridad',
            ),
            176 => 
            array (
                'codigo' => '80300',
                'valores' => 'actividades de investigacion',
            ),
            177 => 
            array (
                'codigo' => '81100',
                'valores' => 'actividades combinadas de mantenimiento de edificios e instalaciones',
            ),
            178 => 
            array (
                'codigo' => '81210',
                'valores' => 'limpieza general de edificios',
            ),
            179 => 
            array (
                'codigo' => '81290',
                'valores' => 'otras actividades combinadas de mantenimiento de edificios e instalaciones ncp',
            ),
            180 => 
            array (
                'codigo' => '81300',
                'valores' => 'servicio de jardineria',
            ),
            181 => 
            array (
                'codigo' => '82110',
                'valores' => 'servicios administrativos de oficinas',
            ),
            182 => 
            array (
                'codigo' => '82190',
                'valores' => 'servicio de fotocopiado y similares, excepto en imprentas',
            ),
            183 => 
            array (
                'codigo' => '82200',
            'valores' => 'actividades de las centrales de llamadas (call center)',
            ),
            184 => 
            array (
                'codigo' => '82300',
                'valores' => 'organizacion de convenciones y ferias de negocios',
            ),
            185 => 
            array (
                'codigo' => '82910',
                'valores' => 'actividades de agencias de cobro y oficinas de credito',
            ),
            186 => 
            array (
                'codigo' => '82921',
                'valores' => 'servicios de envase y empaque de productos alimenticios',
            ),
            187 => 
            array (
                'codigo' => '82922',
                'valores' => 'servicios de envase y empaque de productos medicinales',
            ),
            188 => 
            array (
                'codigo' => '82929',
                'valores' => 'servicio de envase y empaque ncp',
            ),
            189 => 
            array (
                'codigo' => '82990',
                'valores' => 'actividades de apoyo empresariales ncp',
            ),
            190 => 
            array (
                'codigo' => '84110',
                'valores' => 'actividades de la administracion publica en general',
            ),
            191 => 
            array (
                'codigo' => '84111',
                'valores' => 'alcaldias municipales',
            ),
            192 => 
            array (
                'codigo' => '84120',
                'valores' => 'regulacion de las actividades de prestacion de servicios sanitarios, educativos, culturales y otros servicios sociales, excepto seguridad social',
            ),
            193 => 
            array (
                'codigo' => '84130',
                'valores' => 'regulacion y facilitacion de la actividad economica',
            ),
            194 => 
            array (
                'codigo' => '84210',
                'valores' => 'actividades de administracion y funcionamiento del ministerio de relaciones exteriores',
            ),
            195 => 
            array (
                'codigo' => '84220',
                'valores' => 'actividades de defensa',
            ),
            196 => 
            array (
                'codigo' => '84230',
                'valores' => 'actividades de mantenimiento del orden publico y de seguridad',
            ),
            197 => 
            array (
                'codigo' => '84300',
                'valores' => 'actividades de planes de seguridad social de afiliacion obligatoria',
            ),
            198 => 
            array (
                'codigo' => '85101',
                'valores' => 'guarderia educativa',
            ),
            199 => 
            array (
                'codigo' => '85102',
                'valores' => 'ensenanza preescolar o parvularia',
            ),
            200 => 
            array (
                'codigo' => '85103',
                'valores' => 'ensenanza primaria',
            ),
            201 => 
            array (
                'codigo' => '85104',
                'valores' => 'servicio de educacion preescolar y primaria integrada',
            ),
            202 => 
            array (
                'codigo' => '85211',
            'valores' => 'ensenanza secundaria tercer ciclo (7°, 8° y 9°)',
            ),
            203 => 
            array (
                'codigo' => '85212',
                'valores' => 'ensenanza secundaria de formacion general bachillerato',
            ),
            204 => 
            array (
                'codigo' => '85221',
                'valores' => 'ensenanza secundaria de formacion tecnica y profesional',
            ),
            205 => 
            array (
                'codigo' => '85222',
                'valores' => 'ensenanza secundaria de formacion tecnica y profesional integrada con ensenanza primaria',
            ),
            206 => 
            array (
                'codigo' => '85301',
                'valores' => 'ensenanza superior universitaria',
            ),
            207 => 
            array (
                'codigo' => '85302',
                'valores' => 'ensenanza superior no universitaria',
            ),
            208 => 
            array (
                'codigo' => '85303',
                'valores' => 'ensenanza superior integrada a educacion secundaria y/o primaria',
            ),
            209 => 
            array (
                'codigo' => '85410',
                'valores' => 'educacion deportiva y recreativa',
            ),
            210 => 
            array (
                'codigo' => '85420',
                'valores' => 'educacion cultural',
            ),
            211 => 
            array (
                'codigo' => '85490',
                'valores' => 'otros tipos de ensenanza n.c.p.',
            ),
            212 => 
            array (
                'codigo' => '85499',
                'valores' => 'ensenanza formal',
            ),
            213 => 
            array (
                'codigo' => '85500',
                'valores' => 'servicios de apoyo a la ensenanza',
            ),
            214 => 
            array (
                'codigo' => '86100',
                'valores' => 'actividades de hospitales',
            ),
            215 => 
            array (
                'codigo' => '86201',
                'valores' => 'clinicas medicas',
            ),
            216 => 
            array (
                'codigo' => '86202',
                'valores' => 'servicios de odontologia',
            ),
            217 => 
            array (
                'codigo' => '86203',
                'valores' => 'servicios medicos',
            ),
            218 => 
            array (
                'codigo' => '86901',
                'valores' => 'servicios de analisis y estudios de diagnostico',
            ),
            219 => 
            array (
                'codigo' => '86902',
                'valores' => 'actividades de atencion de la salud humana',
            ),
            220 => 
            array (
                'codigo' => '86909',
                'valores' => 'otros servicio relacionados con la salud ncp',
            ),
            221 => 
            array (
                'codigo' => '87100',
                'valores' => 'residencias de ancianos con atencion de enfermeria',
            ),
            222 => 
            array (
                'codigo' => '87200',
                'valores' => 'instituciones dedicadas al tratamiento del retraso mental, problemas de salud mental y el uso indebido de sustancias nocivas',
            ),
            223 => 
            array (
                'codigo' => '87300',
                'valores' => 'instituciones dedicadas al cuidado de ancianos y discapacitados',
            ),
            224 => 
            array (
                'codigo' => '87900',
                'valores' => 'actividades de asistencia a ninos y jovenes',
            ),
            225 => 
            array (
                'codigo' => '87901',
                'valores' => 'otras actividades de atencion en instituciones',
            ),
            226 => 
            array (
                'codigo' => '88100',
                'valores' => 'actividades de asistencia sociales sin alojamiento para ancianos y discapacitados',
            ),
            227 => 
            array (
                'codigo' => '88900',
                'valores' => 'servicios sociales sin alojamiento ncp',
            ),
            228 => 
            array (
                'codigo' => '90000',
                'valores' => 'actividades creativas artisticas y de esparcimiento',
            ),
            229 => 
            array (
                'codigo' => '91010',
                'valores' => 'actividades de bibliotecas y archivos',
            ),
            230 => 
            array (
                'codigo' => '91020',
                'valores' => 'actividades de museos y preservacion de lugares y edificios historicos',
            ),
            231 => 
            array (
                'codigo' => '91030',
                'valores' => 'actividades de jardines botánicos, zoologicos y de reservas naturales',
            ),
            232 => 
            array (
                'codigo' => '92000',
                'valores' => 'actividades de juegos y apuestas',
            ),
            233 => 
            array (
                'codigo' => '93110',
                'valores' => 'gestion de instalaciones deportivas',
            ),
            234 => 
            array (
                'codigo' => '93120',
                'valores' => 'actividades de clubes deportivos',
            ),
            235 => 
            array (
                'codigo' => '93190',
                'valores' => 'otras actividades deportivas',
            ),
            236 => 
            array (
                'codigo' => '93210',
                'valores' => 'actividades de parques de atracciones y parques tematicos',
            ),
            237 => 
            array (
                'codigo' => '93291',
                'valores' => 'discotecas y salas de baile',
            ),
            238 => 
            array (
                'codigo' => '93298',
                'valores' => 'centros vacacionales',
            ),
            239 => 
            array (
                'codigo' => '93299',
                'valores' => 'actividades de esparcimiento ncp',
            ),
            240 => 
            array (
                'codigo' => '94110',
                'valores' => 'actividades de organizaciones empresariales y de empleadores',
            ),
            241 => 
            array (
                'codigo' => '94120',
                'valores' => 'actividades de organizaciones profesionales',
            ),
            242 => 
            array (
                'codigo' => '94200',
                'valores' => 'actividades de sindicatos',
            ),
            243 => 
            array (
                'codigo' => '94910',
                'valores' => 'actividades de organizaciones religiosas',
            ),
            244 => 
            array (
                'codigo' => '94920',
                'valores' => 'actividades de organizaciones politicas',
            ),
            245 => 
            array (
                'codigo' => '94990',
                'valores' => 'actividades de asociaciones n.c.p.',
            ),
            246 => 
            array (
                'codigo' => '95110',
                'valores' => 'reparacion de computadoras y equipo periferico',
            ),
            247 => 
            array (
                'codigo' => '95120',
                'valores' => 'reparacion de equipo de comunicacion',
            ),
            248 => 
            array (
                'codigo' => '95210',
                'valores' => 'reparacion de aparatos electronicos de consumo',
            ),
            249 => 
            array (
                'codigo' => '95220',
                'valores' => 'reparacion de aparatos domesticos y equipo de hogar y jardin',
            ),
            250 => 
            array (
                'codigo' => '95230',
                'valores' => 'reparacion de calzado y articulos de cuero',
            ),
            251 => 
            array (
                'codigo' => '95240',
                'valores' => 'reparacion de muebles y accesorios para el hogar',
            ),
            252 => 
            array (
                'codigo' => '95291',
                'valores' => 'reparacion de instrumentos musicales',
            ),
            253 => 
            array (
                'codigo' => '95292',
                'valores' => 'servicios de cerrajeria y copiado de llaves',
            ),
            254 => 
            array (
                'codigo' => '95293',
                'valores' => 'reparacion de joyas y relojes',
            ),
            255 => 
            array (
                'codigo' => '95294',
                'valores' => 'reparacion de bicicletas, sillas de ruedas y rodados n.c.p.',
            ),
            256 => 
            array (
                'codigo' => '95299',
                'valores' => 'reparaciones de enseres personales n.c.p.',
            ),
            257 => 
            array (
                'codigo' => '96010',
                'valores' => 'lavado y limpieza de prendas de tela y de piel, incluso la limpieza en seco',
            ),
            258 => 
            array (
                'codigo' => '96020',
                'valores' => 'peluqueria y otros tratamientos de belleza',
            ),
            259 => 
            array (
                'codigo' => '96030',
                'valores' => 'pompas funebres y actividades conexas',
            ),
            260 => 
            array (
                'codigo' => '96091',
                'valores' => 'servicios de sauna y otros servicios para la estetica corporal n.c.p.',
            ),
            261 => 
            array (
                'codigo' => '96092',
                'valores' => 'servicios n.c.p.',
            ),
            262 => 
            array (
                'codigo' => '97000',
                'valores' => 'actividad de los hogares en calidad de empleadores de personal domestico',
            ),
            263 => 
            array (
                'codigo' => '98100',
                'valores' => 'actividades indiferenciadas de produccion de bienes de los hogares privados para uso propio',
            ),
            264 => 
            array (
                'codigo' => '98200',
                'valores' => 'actividades indiferenciadas de produccion de servicios de los hogares privados para uso propio',
            ),
            265 => 
            array (
                'codigo' => '99000',
                'valores' => 'actividades de organizaciones y organos extraterritoriales',
            ),
            266 => 
            array (
                'codigo' => '10001',
                'valores' => 'empleados',
            ),
            267 => 
            array (
                'codigo' => '10002',
                'valores' => 'jubilado',
            ),
            268 => 
            array (
                'codigo' => '10003',
                'valores' => 'estudiante',
            ),
            269 => 
            array (
                'codigo' => '10004',
                'valores' => 'desempleado',
            ),
            270 => 
            array (
                'codigo' => '10005',
                'valores' => 'otros',
            ),
        ));
        
        
    }
}