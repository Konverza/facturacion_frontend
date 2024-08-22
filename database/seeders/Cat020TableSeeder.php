<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Cat020TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_020')->delete();
        
        \DB::table('cat_020')->insert(array (
            0 => 
            array (
                'codigo' => '9320',
                'valores' => 'anguila',
            ),
            1 => 
            array (
                'codigo' => '9539',
                'valores' => 'islas turcas y caicos',
            ),
            2 => 
            array (
                'codigo' => '9565',
                'valores' => 'lituania',
            ),
            3 => 
            array (
                'codigo' => '9905',
            'valores' => 'dakota del sur (usa)',
            ),
            4 => 
            array (
                'codigo' => '9999',
                'valores' => 'no definido en migracion',
            ),
            5 => 
            array (
                'codigo' => '9303',
                'valores' => 'afganistan',
            ),
            6 => 
            array (
                'codigo' => '9306',
                'valores' => 'albania',
            ),
            7 => 
            array (
                'codigo' => '9309',
                'valores' => 'alemania occid',
            ),
            8 => 
            array (
                'codigo' => '9310',
                'valores' => 'alemania orient',
            ),
            9 => 
            array (
                'codigo' => '9315',
                'valores' => 'alto volta',
            ),
            10 => 
            array (
                'codigo' => '9317',
                'valores' => 'andorra',
            ),
            11 => 
            array (
                'codigo' => '9318',
                'valores' => 'angola',
            ),
            12 => 
            array (
                'codigo' => '9319',
                'valores' => 'antig y barbuda',
            ),
            13 => 
            array (
                'codigo' => '9324',
                'valores' => 'arabia saudita',
            ),
            14 => 
            array (
                'codigo' => '9327',
                'valores' => 'argelia',
            ),
            15 => 
            array (
                'codigo' => '9330',
                'valores' => 'argentina',
            ),
            16 => 
            array (
                'codigo' => '9333',
                'valores' => 'australia',
            ),
            17 => 
            array (
                'codigo' => '9336',
                'valores' => 'austria',
            ),
            18 => 
            array (
                'codigo' => '9339',
                'valores' => 'bangladesh',
            ),
            19 => 
            array (
                'codigo' => '9342',
                'valores' => 'bahrein',
            ),
            20 => 
            array (
                'codigo' => '9345',
                'valores' => 'barbados',
            ),
            21 => 
            array (
                'codigo' => '9348',
                'valores' => 'belgica',
            ),
            22 => 
            array (
                'codigo' => '9349',
                'valores' => 'belice',
            ),
            23 => 
            array (
                'codigo' => '9350',
                'valores' => 'benin',
            ),
            24 => 
            array (
                'codigo' => '9354',
                'valores' => 'birmania',
            ),
            25 => 
            array (
                'codigo' => '9357',
                'valores' => 'bolivia',
            ),
            26 => 
            array (
                'codigo' => '9360',
                'valores' => 'botswana',
            ),
            27 => 
            array (
                'codigo' => '9363',
                'valores' => 'brasil',
            ),
            28 => 
            array (
                'codigo' => '9366',
                'valores' => 'brunei',
            ),
            29 => 
            array (
                'codigo' => '9372',
                'valores' => 'burundi',
            ),
            30 => 
            array (
                'codigo' => '9374',
                'valores' => 'bophuthatswana',
            ),
            31 => 
            array (
                'codigo' => '9375',
                'valores' => 'butan',
            ),
            32 => 
            array (
                'codigo' => '9377',
                'valores' => 'cabo verde',
            ),
            33 => 
            array (
                'codigo' => '9378',
                'valores' => 'camboya',
            ),
            34 => 
            array (
                'codigo' => '9381',
                'valores' => 'camerun',
            ),
            35 => 
            array (
                'codigo' => '9384',
                'valores' => 'canada',
            ),
            36 => 
            array (
                'codigo' => '9387',
                'valores' => 'ceilan',
            ),
            37 => 
            array (
                'codigo' => '9390',
                'valores' => 'ctro afric rep',
            ),
            38 => 
            array (
                'codigo' => '9393',
                'valores' => 'colombia',
            ),
            39 => 
            array (
                'codigo' => '9394',
                'valores' => 'comoras-islas',
            ),
            40 => 
            array (
                'codigo' => '9396',
                'valores' => 'congo rep del',
            ),
            41 => 
            array (
                'codigo' => '9399',
                'valores' => 'congo rep democ',
            ),
            42 => 
            array (
                'codigo' => '9402',
                'valores' => 'corea norte',
            ),
            43 => 
            array (
                'codigo' => '9405',
                'valores' => 'corea sur',
            ),
            44 => 
            array (
                'codigo' => '9408',
                'valores' => 'costa de marfil',
            ),
            45 => 
            array (
                'codigo' => '9411',
                'valores' => 'costa rica',
            ),
            46 => 
            array (
                'codigo' => '9414',
                'valores' => 'cuba',
            ),
            47 => 
            array (
                'codigo' => '9417',
                'valores' => 'chad',
            ),
            48 => 
            array (
                'codigo' => '9420',
                'valores' => 'checoslovaquia',
            ),
            49 => 
            array (
                'codigo' => '9423',
                'valores' => 'chile',
            ),
            50 => 
            array (
                'codigo' => '9426',
                'valores' => 'china rep popul',
            ),
            51 => 
            array (
                'codigo' => '9432',
                'valores' => 'chipre',
            ),
            52 => 
            array (
                'codigo' => '9435',
                'valores' => 'dahomey',
            ),
            53 => 
            array (
                'codigo' => '9438',
                'valores' => 'dinamarca',
            ),
            54 => 
            array (
                'codigo' => '9440',
                'valores' => 'dominica',
            ),
            55 => 
            array (
                'codigo' => '9441',
                'valores' => 'dominicana rep',
            ),
            56 => 
            array (
                'codigo' => '9444',
                'valores' => 'ecuador',
            ),
            57 => 
            array (
                'codigo' => '9446',
                'valores' => 'emirat arab uni',
            ),
            58 => 
            array (
                'codigo' => '9447',
                'valores' => 'espana',
            ),
            59 => 
            array (
                'codigo' => '9450',
                'valores' => 'ee uu',
            ),
            60 => 
            array (
                'codigo' => '9453',
                'valores' => 'etiopia',
            ),
            61 => 
            array (
                'codigo' => '9456',
                'valores' => 'fiji-islas',
            ),
            62 => 
            array (
                'codigo' => '9459',
                'valores' => 'filipinas',
            ),
            63 => 
            array (
                'codigo' => '9462',
                'valores' => 'finlandia',
            ),
            64 => 
            array (
                'codigo' => '9465',
                'valores' => 'francia',
            ),
            65 => 
            array (
                'codigo' => '9468',
                'valores' => 'gabon',
            ),
            66 => 
            array (
                'codigo' => '9471',
                'valores' => 'gambia',
            ),
            67 => 
            array (
                'codigo' => '9474',
                'valores' => 'ghana',
            ),
            68 => 
            array (
                'codigo' => '9477',
                'valores' => 'gibraltar',
            ),
            69 => 
            array (
                'codigo' => '9480',
                'valores' => 'grecia',
            ),
            70 => 
            array (
                'codigo' => '9481',
                'valores' => 'grenada',
            ),
            71 => 
            array (
                'codigo' => '9483',
                'valores' => 'guatemala',
            ),
            72 => 
            array (
                'codigo' => '9486',
                'valores' => 'guinea',
            ),
            73 => 
            array (
                'codigo' => '9487',
                'valores' => 'guyana',
            ),
            74 => 
            array (
                'codigo' => '9495',
                'valores' => 'haiti',
            ),
            75 => 
            array (
                'codigo' => '9498',
                'valores' => 'holanda',
            ),
            76 => 
            array (
                'codigo' => '9501',
                'valores' => 'honduras',
            ),
            77 => 
            array (
                'codigo' => '9504',
                'valores' => 'hong kong',
            ),
            78 => 
            array (
                'codigo' => '9507',
                'valores' => 'hungria',
            ),
            79 => 
            array (
                'codigo' => '9513',
                'valores' => 'indonesia',
            ),
            80 => 
            array (
                'codigo' => '9516',
                'valores' => 'irak',
            ),
            81 => 
            array (
                'codigo' => '9519',
                'valores' => 'iran',
            ),
            82 => 
            array (
                'codigo' => '9522',
                'valores' => 'irlanda',
            ),
            83 => 
            array (
                'codigo' => '9525',
                'valores' => 'islandia',
            ),
            84 => 
            array (
                'codigo' => '9526',
                'valores' => 'islas salomon',
            ),
            85 => 
            array (
                'codigo' => '9528',
                'valores' => 'israel',
            ),
            86 => 
            array (
                'codigo' => '9531',
                'valores' => 'italia',
            ),
            87 => 
            array (
                'codigo' => '9534',
                'valores' => 'jamaica',
            ),
            88 => 
            array (
                'codigo' => '9537',
                'valores' => 'japon',
            ),
            89 => 
            array (
                'codigo' => '9540',
                'valores' => 'jordania',
            ),
            90 => 
            array (
                'codigo' => '9543',
                'valores' => 'kenia',
            ),
            91 => 
            array (
                'codigo' => '9544',
                'valores' => 'kiribati',
            ),
            92 => 
            array (
                'codigo' => '9546',
                'valores' => 'kuwait',
            ),
            93 => 
            array (
                'codigo' => '9549',
                'valores' => 'laos',
            ),
            94 => 
            array (
                'codigo' => '9552',
                'valores' => 'lesoto',
            ),
            95 => 
            array (
                'codigo' => '9555',
                'valores' => 'libano',
            ),
            96 => 
            array (
                'codigo' => '9558',
                'valores' => 'liberia',
            ),
            97 => 
            array (
                'codigo' => '9561',
                'valores' => 'libia',
            ),
            98 => 
            array (
                'codigo' => '9564',
                'valores' => 'liechtenstein',
            ),
            99 => 
            array (
                'codigo' => '9567',
                'valores' => 'luxemburgo',
            ),
            100 => 
            array (
                'codigo' => '9570',
                'valores' => 'madagascar',
            ),
            101 => 
            array (
                'codigo' => '9573',
                'valores' => 'malasia',
            ),
            102 => 
            array (
                'codigo' => '9576',
                'valores' => 'malawi',
            ),
            103 => 
            array (
                'codigo' => '9577',
                'valores' => 'maldivas',
            ),
            104 => 
            array (
                'codigo' => '9582',
                'valores' => 'malta',
            ),
            105 => 
            array (
                'codigo' => '9585',
                'valores' => 'marruecos',
            ),
            106 => 
            array (
                'codigo' => '9591',
                'valores' => 'mascate y oman',
            ),
            107 => 
            array (
                'codigo' => '9594',
                'valores' => 'mauricio',
            ),
            108 => 
            array (
                'codigo' => '9597',
                'valores' => 'mauritania',
            ),
            109 => 
            array (
                'codigo' => '9600',
                'valores' => 'mexico',
            ),
            110 => 
            array (
                'codigo' => '9601',
                'valores' => 'micronesia',
            ),
            111 => 
            array (
                'codigo' => '9603',
                'valores' => 'monaco',
            ),
            112 => 
            array (
                'codigo' => '9606',
                'valores' => 'mongolia',
            ),
            113 => 
            array (
                'codigo' => '9609',
                'valores' => 'mozambique',
            ),
            114 => 
            array (
                'codigo' => '9611',
                'valores' => 'nauru',
            ),
            115 => 
            array (
                'codigo' => '9612',
                'valores' => 'nepal',
            ),
            116 => 
            array (
                'codigo' => '9615',
                'valores' => 'nicaragua',
            ),
            117 => 
            array (
                'codigo' => '9618',
                'valores' => 'niger',
            ),
            118 => 
            array (
                'codigo' => '9621',
                'valores' => 'nigeria',
            ),
            119 => 
            array (
                'codigo' => '9624',
                'valores' => 'noruega',
            ),
            120 => 
            array (
                'codigo' => '9627',
                'valores' => 'nva caledonia',
            ),
            121 => 
            array (
                'codigo' => '9633',
                'valores' => 'nva zelanda',
            ),
            122 => 
            array (
                'codigo' => '9636',
                'valores' => 'nuevas hebridas',
            ),
            123 => 
            array (
                'codigo' => '9638',
                'valores' => 'papua nv guinea',
            ),
            124 => 
            array (
                'codigo' => '9639',
                'valores' => 'pakistan',
            ),
            125 => 
            array (
                'codigo' => '9642',
                'valores' => 'panama',
            ),
            126 => 
            array (
                'codigo' => '9645',
                'valores' => 'paraguay',
            ),
            127 => 
            array (
                'codigo' => '9648',
                'valores' => 'peru',
            ),
            128 => 
            array (
                'codigo' => '9651',
                'valores' => 'polonia',
            ),
            129 => 
            array (
                'codigo' => '9660',
                'valores' => 'qatar el',
            ),
            130 => 
            array (
                'codigo' => '9663',
                'valores' => 'reino unido',
            ),
            131 => 
            array (
                'codigo' => '9666',
                'valores' => 'egipto',
            ),
            132 => 
            array (
                'codigo' => '9669',
                'valores' => 'rodesia',
            ),
            133 => 
            array (
                'codigo' => '9672',
                'valores' => 'ruanda',
            ),
            134 => 
            array (
                'codigo' => '9675',
                'valores' => 'rumania',
            ),
            135 => 
            array (
                'codigo' => '9677',
                'valores' => 'san marino',
            ),
            136 => 
            array (
                'codigo' => '9678',
                'valores' => 'samoa occid',
            ),
            137 => 
            array (
                'codigo' => '9679',
                'valores' => 'saint kitts and nevis',
            ),
            138 => 
            array (
                'codigo' => '9680',
                'valores' => 'santa lucia',
            ),
            139 => 
            array (
                'codigo' => '9681',
                'valores' => 'senegal',
            ),
            140 => 
            array (
                'codigo' => '9682',
                'valores' => 'saotome y princ',
            ),
            141 => 
            array (
                'codigo' => '9683',
                'valores' => 'sn vic y grenad',
            ),
            142 => 
            array (
                'codigo' => '9684',
                'valores' => 'sierra leona',
            ),
            143 => 
            array (
                'codigo' => '9687',
                'valores' => 'singapur',
            ),
            144 => 
            array (
                'codigo' => '9690',
                'valores' => 'siria',
            ),
            145 => 
            array (
                'codigo' => '9691',
                'valores' => 'seychelles',
            ),
            146 => 
            array (
                'codigo' => '9693',
                'valores' => 'somalia',
            ),
            147 => 
            array (
                'codigo' => '9696',
                'valores' => 'sudafrica rep',
            ),
            148 => 
            array (
                'codigo' => '9699',
                'valores' => 'sudan',
            ),
            149 => 
            array (
                'codigo' => '9702',
                'valores' => 'suecia',
            ),
            150 => 
            array (
                'codigo' => '9705',
                'valores' => 'suiza',
            ),
            151 => 
            array (
                'codigo' => '9706',
                'valores' => 'surinam',
            ),
            152 => 
            array (
                'codigo' => '9707',
                'valores' => 'sri lanka',
            ),
            153 => 
            array (
                'codigo' => '9708',
                'valores' => 'suecilandia',
            ),
            154 => 
            array (
                'codigo' => '9714',
                'valores' => 'tanzania',
            ),
            155 => 
            array (
                'codigo' => '9717',
                'valores' => 'togo',
            ),
            156 => 
            array (
                'codigo' => '9720',
                'valores' => 'trinidad tobago',
            ),
            157 => 
            array (
                'codigo' => '9722',
                'valores' => 'tonga',
            ),
            158 => 
            array (
                'codigo' => '9723',
                'valores' => 'tunez',
            ),
            159 => 
            array (
                'codigo' => '9725',
                'valores' => 'transkei',
            ),
            160 => 
            array (
                'codigo' => '9726',
                'valores' => 'turquia',
            ),
            161 => 
            array (
                'codigo' => '9727',
                'valores' => 'tuvalu',
            ),
            162 => 
            array (
                'codigo' => '9729',
                'valores' => 'uganda',
            ),
            163 => 
            array (
                'codigo' => '9732',
                'valores' => 'u r s s',
            ),
            164 => 
            array (
                'codigo' => '9735',
                'valores' => 'uruguay',
            ),
            165 => 
            array (
                'codigo' => '9738',
                'valores' => 'vaticano',
            ),
            166 => 
            array (
                'codigo' => '9739',
                'valores' => 'vanuatu',
            ),
            167 => 
            array (
                'codigo' => '9740',
                'valores' => 'venda',
            ),
            168 => 
            array (
                'codigo' => '9741',
                'valores' => 'venezuela',
            ),
            169 => 
            array (
                'codigo' => '9744',
                'valores' => 'vietnam norte',
            ),
            170 => 
            array (
                'codigo' => '9747',
                'valores' => 'vietnam sur',
            ),
            171 => 
            array (
                'codigo' => '9750',
                'valores' => 'yemen sur rep',
            ),
            172 => 
            array (
                'codigo' => '9756',
                'valores' => 'yugoslavia',
            ),
            173 => 
            array (
                'codigo' => '9758',
                'valores' => 'zaire',
            ),
            174 => 
            array (
                'codigo' => '9759',
                'valores' => 'zambia',
            ),
            175 => 
            array (
                'codigo' => '9760',
                'valores' => 'zimbabwe',
            ),
            176 => 
            array (
                'codigo' => '9850',
                'valores' => 'puerto rico',
            ),
            177 => 
            array (
                'codigo' => '9862',
                'valores' => 'bahamas',
            ),
            178 => 
            array (
                'codigo' => '9863',
                'valores' => 'bermudas',
            ),
            179 => 
            array (
                'codigo' => '9865',
                'valores' => 'martinica',
            ),
            180 => 
            array (
                'codigo' => '9886',
                'valores' => 'nueva guinea',
            ),
            181 => 
            array (
                'codigo' => '9898',
                'valores' => 'ant holandesas',
            ),
            182 => 
            array (
                'codigo' => '9899',
                'valores' => 'taiwan',
            ),
            183 => 
            array (
                'codigo' => '9897',
                'valores' => 'islas virgenes britanicas',
            ),
            184 => 
            array (
                'codigo' => '9887',
                'valores' => 'islas gran caiman',
            ),
            185 => 
            array (
                'codigo' => '9571',
                'valores' => 'macedonia',
            ),
            186 => 
            array (
                'codigo' => '9300',
                'valores' => 'el salvador',
            ),
            187 => 
            array (
                'codigo' => '9369',
                'valores' => 'bulgaria',
            ),
            188 => 
            array (
                'codigo' => '9439',
                'valores' => 'djibouti',
            ),
            189 => 
            array (
                'codigo' => '9510',
                'valores' => 'india',
            ),
            190 => 
            array (
                'codigo' => '9579',
                'valores' => 'mali',
            ),
            191 => 
            array (
                'codigo' => '9654',
                'valores' => 'portugal',
            ),
            192 => 
            array (
                'codigo' => '9711',
                'valores' => 'tailandia',
            ),
            193 => 
            array (
                'codigo' => '9736',
                'valores' => 'ucrania',
            ),
            194 => 
            array (
                'codigo' => '9737',
                'valores' => 'uzbekistan',
            ),
            195 => 
            array (
                'codigo' => '9640',
                'valores' => 'palestina',
            ),
            196 => 
            array (
                'codigo' => '9641',
                'valores' => 'croacia',
            ),
            197 => 
            array (
                'codigo' => '9673',
                'valores' => 'republica de armenia',
            ),
            198 => 
            array (
                'codigo' => '9472',
                'valores' => 'georgia',
            ),
            199 => 
            array (
                'codigo' => '9311',
                'valores' => 'alemania',
            ),
            200 => 
            array (
                'codigo' => '9733',
                'valores' => 'rusia',
            ),
            201 => 
            array (
                'codigo' => '9541',
                'valores' => 'kasakistan',
            ),
            202 => 
            array (
                'codigo' => '9746',
                'valores' => 'vietnam',
            ),
            203 => 
            array (
                'codigo' => '9551',
                'valores' => 'letonia',
            ),
            204 => 
            array (
                'codigo' => '9451',
                'valores' => 'eslovenia',
            ),
            205 => 
            array (
                'codigo' => '9338',
                'valores' => 'azerbaiyan',
            ),
            206 => 
            array (
                'codigo' => '9353',
                'valores' => 'bielorrusia',
            ),
            207 => 
            array (
                'codigo' => '9482',
                'valores' => 'groenlandia',
            ),
            208 => 
            array (
                'codigo' => '9494',
                'valores' => 'guinea-bissau',
            ),
            209 => 
            array (
                'codigo' => '9524',
                'valores' => 'isla de cocos',
            ),
            210 => 
            array (
                'codigo' => '9304',
                'valores' => 'aland',
            ),
            211 => 
            array (
                'codigo' => '9332',
                'valores' => 'aruba',
            ),
            212 => 
            array (
                'codigo' => '9454',
                'valores' => 'eritrea',
            ),
            213 => 
            array (
                'codigo' => '9457',
                'valores' => 'estonia',
            ),
            214 => 
            array (
                'codigo' => '9489',
                'valores' => 'guadalupe',
            ),
            215 => 
            array (
                'codigo' => '9491',
                'valores' => 'guayana francesa',
            ),
            216 => 
            array (
                'codigo' => '9492',
                'valores' => 'guernsey',
            ),
            217 => 
            array (
                'codigo' => '9523',
                'valores' => 'isla de navidad',
            ),
            218 => 
            array (
                'codigo' => '9530',
                'valores' => 'islas azores',
            ),
            219 => 
            array (
                'codigo' => '9532',
                'valores' => 'isla qeshm',
            ),
            220 => 
            array (
                'codigo' => '9535',
                'valores' => 'islas marianas del norte',
            ),
            221 => 
            array (
                'codigo' => '9542',
                'valores' => 'islas ultramarinas de ee uu',
            ),
            222 => 
            array (
                'codigo' => '9547',
                'valores' => 'jersey',
            ),
            223 => 
            array (
                'codigo' => '9548',
                'valores' => 'kirguistan',
            ),
            224 => 
            array (
                'codigo' => '9574',
                'valores' => 'mali',
            ),
            225 => 
            array (
                'codigo' => '9598',
                'valores' => 'mayotte',
            ),
            226 => 
            array (
                'codigo' => '9602',
                'valores' => 'moldavia',
            ),
            227 => 
            array (
                'codigo' => '9607',
                'valores' => 'montenegro',
            ),
            228 => 
            array (
                'codigo' => '9608',
                'valores' => 'monserrat',
            ),
            229 => 
            array (
                'codigo' => '9623',
                'valores' => 'norfolk',
            ),
            230 => 
            array (
                'codigo' => '9652',
                'valores' => 'polinesia francesa',
            ),
            231 => 
            array (
                'codigo' => '9692',
                'valores' => 'svalbard y jan mayen',
            ),
            232 => 
            array (
                'codigo' => '9709',
                'valores' => 'tayikistan',
            ),
            233 => 
            array (
                'codigo' => '9712',
                'valores' => 'territorio britanico del oceano indico',
            ),
            234 => 
            array (
                'codigo' => '9716',
                'valores' => 'timor oriental',
            ),
            235 => 
            array (
                'codigo' => '9718',
                'valores' => 'tokelau',
            ),
            236 => 
            array (
                'codigo' => '9719',
                'valores' => 'turkmenistan',
            ),
            237 => 
            array (
                'codigo' => '9751',
                'valores' => 'yibuti',
            ),
            238 => 
            array (
                'codigo' => '9452',
                'valores' => 'wallis y futuna',
            ),
            239 => 
            array (
                'codigo' => '9901',
            'valores' => 'nevada (usa)',
            ),
            240 => 
            array (
                'codigo' => '9902',
            'valores' => 'wyoming (usa)',
            ),
            241 => 
            array (
                'codigo' => '9903',
                'valores' => 'campione ditalia, italia',
            ),
            242 => 
            array (
                'codigo' => '9664',
                'valores' => 'republica checa',
            ),
            243 => 
            array (
                'codigo' => '9415',
                'valores' => 'curacao',
            ),
            244 => 
            array (
                'codigo' => '9904',
            'valores' => 'florida (usa)',
            ),
            245 => 
            array (
                'codigo' => '9514',
                'valores' => 'inglaterra y gales',
            ),
            246 => 
            array (
                'codigo' => '9906',
            'valores' => 'texas (usa)',
            ),
            247 => 
            array (
                'codigo' => '9359',
                'valores' => 'bosnia y herzegovina',
            ),
            248 => 
            array (
                'codigo' => '9493',
                'valores' => 'guinea ecuatorial',
            ),
            249 => 
            array (
                'codigo' => '9521',
                'valores' => 'isla de man',
            ),
            250 => 
            array (
                'codigo' => '9533',
                'valores' => 'islas malvinas',
            ),
            251 => 
            array (
                'codigo' => '9538',
                'valores' => 'islas pitcairn',
            ),
            252 => 
            array (
                'codigo' => '9689',
                'valores' => 'serbia',
            ),
            253 => 
            array (
                'codigo' => '9713',
                'valores' => 'territorios australes franceses',
            ),
            254 => 
            array (
                'codigo' => '9449',
                'valores' => 'eslovaquia',
            ),
            255 => 
            array (
                'codigo' => '9888',
                'valores' => 'san maarten',
            ),
            256 => 
            array (
                'codigo' => '9490',
                'valores' => 'guam',
            ),
            257 => 
            array (
                'codigo' => '9527',
                'valores' => 'islas cook',
            ),
            258 => 
            array (
                'codigo' => '9529',
                'valores' => 'islas feroe',
            ),
            259 => 
            array (
                'codigo' => '9536',
                'valores' => 'islas marshall',
            ),
            260 => 
            array (
                'codigo' => '9545',
                'valores' => 'islas virgenes estadounidenses',
            ),
            261 => 
            array (
                'codigo' => '9568',
                'valores' => 'macao',
            ),
            262 => 
            array (
                'codigo' => '9610',
                'valores' => 'namibia',
            ),
            263 => 
            array (
                'codigo' => '9622',
                'valores' => 'niue',
            ),
            264 => 
            array (
                'codigo' => '9643',
                'valores' => 'palaos',
            ),
            265 => 
            array (
                'codigo' => '9667',
                'valores' => 'reunion',
            ),
            266 => 
            array (
                'codigo' => '9676',
                'valores' => 'sahara occidental',
            ),
            267 => 
            array (
                'codigo' => '9685',
                'valores' => 'samoa americana',
            ),
            268 => 
            array (
                'codigo' => '9686',
                'valores' => 'san pedro y miquelon',
            ),
            269 => 
            array (
                'codigo' => '9688',
                'valores' => 'santa elena',
            ),
            270 => 
            array (
                'codigo' => '9715',
                'valores' => 'territorios palestinos',
            ),
            271 => 
            array (
                'codigo' => '9900',
            'valores' => 'delaware (usa)',
            ),
            272 => 
            array (
                'codigo' => '9371',
                'valores' => 'burkina faso',
            ),
            273 => 
            array (
                'codigo' => '9376',
                'valores' => 'cabinda',
            ),
            274 => 
            array (
                'codigo' => '9907',
            'valores' => 'washington (usa)',
            ),
        ));
        
        
    }
}