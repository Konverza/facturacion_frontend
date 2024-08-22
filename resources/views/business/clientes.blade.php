@extends('layouts.app')
@include('business.layouts.navbar')

@section('content')
    <div class="container-fluid">
        <div class="row mt-5 mb-2">
            <div class="col-md-12">
                <div class="header-content">
                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <h1 class="header-title text-center">Clientes Registrados</h1>
                        </div>
                        <div class="col-lg-4 text-right">
                            <!-- Button trigger modal Agregar DTE Físico-->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#aggCliente">
                                Agregar Cliente
                            </button>

                            <!-- Modal DTE Fisico -->
                            <div class="modal modal-lg fade" id="aggCliente" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="aggCliente">Nuevo Cliente</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title text-muted text-dark font-weight-normal">
                                                        Agregar Nuevo Cliente</h5>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="bg-light mb-3 mt-3">
                                                            <div class="card-body fw-bold">
                                                                <div class="tab-content" id="myTabContent">
                                                                    <div class="tab-pane fade show active" id="receptor"
                                                                        role="tabpanel" aria-labelledby="receptor">
                                                                        <form>
                                                                            <div class="form-group">
                                                                                <label for="nit">NIT:</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="nit"
                                                                                    placeholder="Digite el número de NIT del emisor">
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        <label for="nombreCliente">Nombre
                                                                                            del
                                                                                            Cliente:</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="nombreCliente"
                                                                                            placeholder="Nombre completo del cliente">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        <label for="nrc">NRC:</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="nrc" placeholder="">
                                                                                    </div>

                                                                                </div>
                                                                            </div>




                                                                            <div class="form-group">
                                                                                <label for="nombreComercial">Nombre
                                                                                    Comercial:</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="nombreComercial"
                                                                                    placeholder="Personalizar nombre comercial">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="actividadEconomica">Actividad
                                                                                    Económica:</label>
                                                                                <select class="form-select"
                                                                                    id="actividadEconomica">
                                                                                    <option value="01">Actividad económica 1
                                                                                    </option>
                                                                                    <option value="02">Actividad económica 2
                                                                                    </option>
                                                                                    <option value="03">Actividad económica 3
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="departamento">Departamento:</label>
                                                                                        <select class="form-select"
                                                                                            id="departamentoContribuyente">
                                                                                            <option value="01">01 -
                                                                                                AHUACHAPAN</option>
                                                                                            <option value="02">02 -
                                                                                                SANTA ANA</option>
                                                                                            <option value="03">03 -
                                                                                                SONSONATE</option>
                                                                                            <option value="04">04 -
                                                                                                CHALATENANGO
                                                                                            </option>
                                                                                            <option value="05">05 - LA
                                                                                                LIBERTAD
                                                                                            </option>
                                                                                            <option value="06" selected>
                                                                                                06 - SAN
                                                                                                SALVADOR</option>
                                                                                            <option value="07">07 -
                                                                                                CUSCATLAN</option>
                                                                                            <option value="08">08 - LA
                                                                                                PAZ</option>
                                                                                            <option value="09">09 -
                                                                                                CABAÑAS</option>
                                                                                            <option value="10">10 - SAN
                                                                                                VICENTE
                                                                                            </option>
                                                                                            <option value="11">11 -
                                                                                                USULUTAN</option>
                                                                                            <option value="12">12 - SAN
                                                                                                MIGUEL</option>
                                                                                            <option value="13">13 -
                                                                                                MORAZAN</option>
                                                                                            <option value="14">14 - LA
                                                                                                UNION</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="municipio">Municipio:</label>
                                                                                        <select class="form-select"
                                                                                            id="municipioContribuyente">
                                                                                            <option value="14" selected>
                                                                                                14 - SAN
                                                                                                SALVADOR</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label for="direccionComplemento">Dirección
                                                                                    Complemento:</label>
                                                                                <textarea class="form-control" id="direccionComplemento" placeholder="Digite el complemento de la dirección"></textarea>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        <label for="correo">Correo
                                                                                            electrónico:</label>
                                                                                        <input type="email"
                                                                                            class="form-control"
                                                                                            id="correo"
                                                                                            placeholder="Correo electrónico">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="telefono">Teléfono:</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="telefono"
                                                                                            placeholder="Teléfono">
                                                                                    </div>
                                                                                </div>
                                                                            </div>


                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" class="btn btn-primary">Guardar Cliente</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center row">
            <div class="col-md-10">
                <h4 class="mt-4"></h4>
                <table class="table shadow table-striped table-bordered mb-2">
                    <thead>
                        <tr>
                            <th>NIT</th>
                            <th>NRC</th>
                            <th>Nombre del Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1234-56789123-4567</td>
                            <td>123456-7</td>
                            <td>Nombre: Juan González
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>1234-56789123-4567</td>
                            <td>123456-7</td>
                            <td>Nombre: Sergio Pérez
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        <tr>
                            <td>1234-56789123-4567</td>
                            <td>123456-7</td>
                            <td>Nombre: Ricardo Brown
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>1234-56789123-4567</td>
                            <td>123456-7</td>
                            <td>Nombre: Marcelo Solis
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script>
        const municipiosPorDepartamento = {
            '01': [{
                    value: '01',
                    text: '01 - AHUACHAPÁN'
                },
                {
                    value: '02',
                    text: '02 - APANECA'
                },
                {
                    value: '03',
                    text: '03 - ATIQUIZAYA'
                },
                {
                    value: '04',
                    text: '04 - CONCEPCIÓN DE ATACO'
                },
                {
                    value: '05',
                    text: '05 - EL REFUGIO'
                },
                {
                    value: '06',
                    text: '06 - GUAYMANGO'
                },
                {
                    value: '07',
                    text: '07 - JUJUTLA'
                },
                {
                    value: '08',
                    text: '08 - SAN FRANCISCO MENÉNDEZ'
                },
                {
                    value: '09',
                    text: '09 - SAN LORENZO'
                },
                {
                    value: '10',
                    text: '10 - SAN PEDRO PUXTLA'
                },
                {
                    value: '11',
                    text: '11 - TACUBA'
                },
                {
                    value: '12',
                    text: '12 - TURÍN'
                }
            ],
            '02': [{
                    value: '01',
                    text: '01 - CANDELARIA DE LA FRONTERA'
                },
                {
                    value: '02',
                    text: '02 - COATEPEQUE'
                },
                {
                    value: '03',
                    text: '03 - CHALCHUAPA'
                },
                {
                    value: '04',
                    text: '04 - EL CONGO'
                },
                {
                    value: '05',
                    text: '05 - EL PORVENIR'
                },
                {
                    value: '06',
                    text: '06 - MASAHUAT'
                },
                {
                    value: '07',
                    text: '07 - METAPÁN'
                },
                {
                    value: '08',
                    text: '08 - SAN ANTONIO PAJONAL'
                },
                {
                    value: '09',
                    text: '09 - SAN SEBASTIÁN SALITRILLO'
                },
                {
                    value: '10',
                    text: '10 - SANTA ANA'
                },
                {
                    value: '11',
                    text: '11 - STA ROSA GUACHI'
                },
                {
                    value: '12',
                    text: '12 - STGO D LA FRONT'
                },
                {
                    value: '13',
                    text: '13 - TEXISTEPEQUE'
                }
            ],
            '03': [{
                    value: '01',
                    text: '01 - ACAJUTLA'
                },
                {
                    value: '02',
                    text: '02 - ARMENIA'
                },
                {
                    value: '03',
                    text: '03 - CALUCO'
                },
                {
                    value: '04',
                    text: '04 - CUISNAHUAT'
                },
                {
                    value: '05',
                    text: '05 - STA I ISHUATAN'
                },
                {
                    value: '06',
                    text: '06 - IZALCO'
                },
                {
                    value: '07',
                    text: '07 - JUAYÚA'
                },
                {
                    value: '08',
                    text: '08 - NAHUIZALCO'
                },
                {
                    value: '09',
                    text: '09 - NAHULINGO'
                },
                {
                    value: '10',
                    text: '10 - SALCOATITÁN'
                },
                {
                    value: '11',
                    text: '11 - SAN ANTONIO DEL MONTE'
                },
                {
                    value: '12',
                    text: '12 - SAN JULIÁN'
                },
                {
                    value: '13',
                    text: '13 - STA C MASAHUAT'
                },
                {
                    value: '14',
                    text: '14 - SANTO DOMINGO GUZMÁN'
                },
                {
                    value: '15',
                    text: '15 - SONSONATE'
                },
                {
                    value: '16',
                    text: '16 - SONZACATE'
                }
            ],
            '04': [{
                    value: '01',
                    text: '01 - AGUA CALIENTE'
                },
                {
                    value: '02',
                    text: '02 - ARCATAO'
                },
                {
                    value: '03',
                    text: '03 - AZACUALPA'
                },
                {
                    value: '04',
                    text: '04 - CITALÁ'
                },
                {
                    value: '05',
                    text: '05 - COMALAPA'
                },
                {
                    value: '06',
                    text: '06 - CONCEPCIÓN QUEZALTEPEQUE'
                },
                {
                    value: '07',
                    text: '07 - CHALATENANGO'
                },
                {
                    value: '08',
                    text: '08 - DULCE NOM MARÍA'
                },
                {
                    value: '09',
                    text: '09 - EL CARRIZAL'
                },
                {
                    value: '10',
                    text: '10 - EL PARAÍSO'
                },
                {
                    value: '11',
                    text: '11 - LA LAGUNA'
                },
                {
                    value: '12',
                    text: '12 - LA PALMA'
                },
                {
                    value: '13',
                    text: '13 - LA REINA'
                },
                {
                    value: '14',
                    text: '14 - LAS VUELTAS'
                },
                {
                    value: '15',
                    text: '15 - NOMBRE DE JESUS'
                },
                {
                    value: '16',
                    text: '16 - NVA CONCEPCIÓN'
                },
                {
                    value: '17',
                    text: '17 - NUEVA TRINIDAD'
                },
                {
                    value: '18',
                    text: '18 - OJOS DE AGUA'
                },
                {
                    value: '19',
                    text: '19 - POTONICO'
                },
                {
                    value: '20',
                    text: '20 - SAN ANT LA CRUZ'
                },
                {
                    value: '21',
                    text: '21 - SAN ANT RANCHOS'
                },
                {
                    value: '22',
                    text: '22 - SAN FERNANDO'
                },
                {
                    value: '23',
                    text: '23 - SAN FRANCISCO LEMPA'
                },
                {
                    value: '24',
                    text: '24 - SAN FRANCISCO MORAZÁN'
                },
                {
                    value: '25',
                    text: '25 - SAN IGNACIO'
                },
                {
                    value: '26',
                    text: '26 - SAN I LABRADOR'
                },
                {
                    value: '27',
                    text: '27 - SAN J CANCASQUE'
                },
                {
                    value: '28',
                    text: '28 - SAN JOSE FLORES'
                },
                {
                    value: '29',
                    text: '29 - SAN LUIS CARMEN'
                },
                {
                    value: '30',
                    text: '30 - SN MIG MERCEDES'
                },
                {
                    value: '31',
                    text: '31 - SAN RAFAEL'
                },
                {
                    value: '32',
                    text: '32 - SANTA RITA'
                },
                {
                    value: '33',
                    text: '33 - TEJUTLA'
                }
            ],
            '05': [{
                    value: '01',
                    text: '01 - ANTGO CUSCATLÁN'
                },
                {
                    value: '02',
                    text: '02 - CIUDAD ARCE'
                },
                {
                    value: '03',
                    text: '03 - COLON'
                },
                {
                    value: '04',
                    text: '04 - COMASAGUA'
                },
                {
                    value: '05',
                    text: '05 - CHILTIUPAN'
                },
                {
                    value: '06',
                    text: '06 - HUIZÚCAR'
                },
                {
                    value: '07',
                    text: '07 - JAYAQUE'
                },
                {
                    value: '08',
                    text: '08 - JICALAPA'
                },
                {
                    value: '09',
                    text: '09 - LA LIBERTAD'
                },
                {
                    value: '10',
                    text: '10 - NUEVO CUSCATLÁN'
                },
                {
                    value: '11',
                    text: '11 - SANTA TECLA'
                },
                {
                    value: '12',
                    text: '12 - QUEZALTEPEQUE'
                },
                {
                    value: '13',
                    text: '13 - SACACOYO'
                },
                {
                    value: '14',
                    text: '14 - SN J VILLANUEVA'
                },
                {
                    value: '15',
                    text: '15 - SAN JUAN OPICO'
                },
                {
                    value: '16',
                    text: '16 - SAN MATÍAS'
                },
                {
                    value: '17',
                    text: '17 - SAN P TACACHICO'
                },
                {
                    value: '18',
                    text: '18 - TAMANIQUE'
                },
                {
                    value: '19',
                    text: '19 - TALNIQUE'
                },
                {
                    value: '20',
                    text: '20 - TEOTEPEQUE'
                },
                {
                    value: '21',
                    text: '21 - TEPECOYO'
                },
                {
                    value: '22',
                    text: '22 - ZARAGOZA'
                }
            ],
            '06': [{
                    value: '01',
                    text: '01 - AGUILARES'
                },
                {
                    value: '02',
                    text: '02 - APOPA'
                },
                {
                    value: '03',
                    text: '03 - AYUTUXTEPEQUE'
                },
                {
                    value: '04',
                    text: '04 - CUSCATANCINGO'
                },
                {
                    value: '05',
                    text: '05 - EL PAISNAL'
                },
                {
                    value: '06',
                    text: '06 - GUAZAPA'
                },
                {
                    value: '07',
                    text: '07 - ILOPANGO'
                },
                {
                    value: '08',
                    text: '08 - MEJICANOS'
                },
                {
                    value: '09',
                    text: '09 - NEJAPA'
                },
                {
                    value: '10',
                    text: '10 - PANCHIMALCO'
                },
                {
                    value: '11',
                    text: '11 - ROSARIO DE MORA'
                },
                {
                    value: '12',
                    text: '12 - SAN MARCOS'
                },
                {
                    value: '13',
                    text: '13 - SAN MARTIN'
                },
                {
                    value: '14',
                    text: '14 - SAN SALVADOR'
                },
                {
                    value: '15',
                    text: '15 - STG TEXACUANGOS'
                },
                {
                    value: '16',
                    text: '16 - SANTO TOMAS'
                },
                {
                    value: '17',
                    text: '17 - SOYAPANGO'
                },
                {
                    value: '18',
                    text: '18 - TONACATEPEQUE'
                },
                {
                    value: '19',
                    text: '19 - CIUDAD DELGADO'
                }
            ],
            '07': [{
                    value: '01',
                    text: '01 - CANDELARIA'
                },
                {
                    value: '02',
                    text: '02 - COJUTEPEQUE'
                },
                {
                    value: '03',
                    text: '03 - EL CARMEN'
                },
                {
                    value: '04',
                    text: '04 - EL ROSARIO'
                },
                {
                    value: '05',
                    text: '05 - MONTE SAN JUAN'
                },
                {
                    value: '06',
                    text: '06 - ORAT CONCEPCIÓN'
                },
                {
                    value: '07',
                    text: '07 - SAN B PERULAPIA'
                },
                {
                    value: '08',
                    text: '08 - SAN CRISTÓBAL'
                },
                {
                    value: '09',
                    text: '09 - SAN J GUAYABAL'
                },
                {
                    value: '10',
                    text: '10 - SAN P PERULAPÁN'
                },
                {
                    value: '11',
                    text: '11 - SAN RAF CEDROS'
                },
                {
                    value: '12',
                    text: '12 - SAN RAMON'
                },
                {
                    value: '13',
                    text: '13 - STA C ANALQUITO'
                },
                {
                    value: '14',
                    text: '14 - STA C MICHAPA'
                },
                {
                    value: '15',
                    text: '15 - SUCHITOTO'
                },
                {
                    value: '16',
                    text: '16 - TENANCINGO'
                }
            ],
            '08': [{
                    value: '01',
                    text: '01 - CUYULTITÁN'
                },
                {
                    value: '02',
                    text: '02 - EL ROSARIO'
                },
                {
                    value: '03',
                    text: '03 - JERUSALÉN'
                },
                {
                    value: '04',
                    text: '04 - MERCED LA CEIBA'
                },
                {
                    value: '05',
                    text: '05 - OLOCUILTA'
                },
                {
                    value: '06',
                    text: '06 - PARAÍSO OSORIO'
                },
                {
                    value: '07',
                    text: '07 - SN ANT MASAHUAT'
                },
                {
                    value: '08',
                    text: '08 - SAN EMIGDIO'
                },
                {
                    value: '09',
                    text: '09 - SN FCO CHINAMEC'
                },
                {
                    value: '10',
                    text: '10 - SAN J NONUALCO'
                },
                {
                    value: '11',
                    text: '11 - SAN JUAN TALPA'
                },
                {
                    value: '12',
                    text: '12 - SAN JUAN TEPEZONTES'
                },
                {
                    value: '13',
                    text: '13 - SAN LUIS TALPA'
                },
                {
                    value: '14',
                    text: '14 - SAN MIGUEL TEPEZONTES'
                },
                {
                    value: '15',
                    text: '15 - SAN PEDRO MASAHUAT'
                },
                {
                    value: '16',
                    text: '16 - SAN PEDRO NONUALCO'
                },
                {
                    value: '17',
                    text: '17 - SAN R OBRAJUELO'
                },
                {
                    value: '18',
                    text: '18 - STA MA OSTUMA'
                },
                {
                    value: '19',
                    text: '19 - STGO NONUALCO'
                },
                {
                    value: '20',
                    text: '20 - TAPALHUACA'
                },
                {
                    value: '21',
                    text: '21 - ZACATECOLUCA'
                },
                {
                    value: '22',
                    text: '22 - SN LUIS LA HERR'
                }
            ],
            '09': [{
                    value: '01',
                    text: '01 - CINQUERA'
                },
                {
                    value: '02',
                    text: '02 - GUACOTECTI'
                },
                {
                    value: '03',
                    text: '03 - ILOBASCO'
                },
                {
                    value: '04',
                    text: '04 - JUTIAPA'
                },
                {
                    value: '05',
                    text: '05 - SAN ISIDRO'
                },
                {
                    value: '06',
                    text: '06 - SENSUNTEPEQUE'
                },
                {
                    value: '07',
                    text: '07 - TEJUTEPEQUE'
                },
                {
                    value: '08',
                    text: '08 - VICTORIA'
                },
                {
                    value: '09',
                    text: '09 - DOLORES'
                }
            ],
            '10': [{
                    value: '01',
                    text: '01 - APASTEPEQUE'
                },
                {
                    value: '02',
                    text: '02 - GUADALUPE'
                },
                {
                    value: '03',
                    text: '03 - SAN CAY ISTEPEQ'
                },
                {
                    value: '04',
                    text: '04 - SANTA CLARA'
                },
                {
                    value: '05',
                    text: '05 - SANTO DOMINGO'
                },
                {
                    value: '06',
                    text: '06 - SN EST CATARINA'
                },
                {
                    value: '07',
                    text: '07 - SAN ILDEFONSO'
                },
                {
                    value: '08',
                    text: '08 - SAN LORENZO'
                },
                {
                    value: '09',
                    text: '09 - SAN SEBASTIÁN'
                },
                {
                    value: '10',
                    text: '10 - SAN VICENTE'
                },
                {
                    value: '11',
                    text: '11 - TECOLUCA'
                },
                {
                    value: '12',
                    text: '12 - TEPETITÁN'
                },
                {
                    value: '13',
                    text: '13 - VERAPAZ'
                }
            ],
            '11': [{
                    value: '01',
                    text: '01 - ALEGRÍA'
                },
                {
                    value: '02',
                    text: '02 - BERLÍN'
                },
                {
                    value: '03',
                    text: '03 - CALIFORNIA'
                },
                {
                    value: '04',
                    text: '04 - CONCEP BATRES'
                },
                {
                    value: '05',
                    text: '05 - EL TRIUNFO'
                },
                {
                    value: '06',
                    text: '06 - EREGUAYQUÍN'
                },
                {
                    value: '07',
                    text: '07 - ESTANZUELAS'
                },
                {
                    value: '08',
                    text: '08 - JIQUILISCO'
                },
                {
                    value: '09',
                    text: '09 - JUCUAPA'
                },
                {
                    value: '10',
                    text: '10 - JUCUARÁN'
                },
                {
                    value: '11',
                    text: '11 - MERCEDES UMAÑA'
                },
                {
                    value: '12',
                    text: '12 - NUEVA GRANADA'
                },
                {
                    value: '13',
                    text: '13 - OZATLÁN'
                },
                {
                    value: '14',
                    text: '14 - PTO EL TRIUNFO'
                },
                {
                    value: '15',
                    text: '15 - SAN AGUSTÍN'
                },
                {
                    value: '16',
                    text: '16 - SN BUENAVENTURA'
                },
                {
                    value: '17',
                    text: '17 - SAN DIONISIO'
                },
                {
                    value: '18',
                    text: '18 - SANTA ELENA'
                },
                {
                    value: '19',
                    text: '19 - SAN FCO JAVIER'
                },
                {
                    value: '20',
                    text: '20 - SANTA MARÍA'
                },
                {
                    value: '21',
                    text: '21 - STGO DE MARÍA'
                },
                {
                    value: '22',
                    text: '22 - TECAPÁN'
                },
                {
                    value: '23',
                    text: '23 - USULUTÁN'
                }
            ],
            '12': [{
                    value: '01',
                    text: '01 - CAROLINA'
                },
                {
                    value: '02',
                    text: '02 - CIUDAD BARRIOS'
                },
                {
                    value: '03',
                    text: '03 - COMACARÁN'
                },
                {
                    value: '04',
                    text: '04 - CHAPELTIQUE'
                },
                {
                    value: '05',
                    text: '05 - CHINAMECA'
                },
                {
                    value: '06',
                    text: '06 - CHIRILAGUA'
                },
                {
                    value: '07',
                    text: '07 - EL TRANSITO'
                },
                {
                    value: '08',
                    text: '08 - LOLOTIQUE'
                },
                {
                    value: '09',
                    text: '09 - MONCAGUA'
                },
                {
                    value: '10',
                    text: '10 - NUEVA GUADALUPE'
                },
                {
                    value: '11',
                    text: '11 - NVO EDÉN S JUAN'
                },
                {
                    value: '12',
                    text: '12 - QUELEPA'
                },
                {
                    value: '13',
                    text: '13 - SAN ANT D MOSCO'
                },
                {
                    value: '14',
                    text: '14 - SAN GERARDO'
                },
                {
                    value: '15',
                    text: '15 - SAN JORGE'
                },
                {
                    value: '16',
                    text: '16 - SAN LUIS REINA'
                },
                {
                    value: '17',
                    text: '17 - SAN MIGUEL'
                },
                {
                    value: '18',
                    text: '18 - SAN RAF ORIENTE'
                },
                {
                    value: '19',
                    text: '19 - SESORI'
                },
                {
                    value: '20',
                    text: '20 - ULUAZAPA'
                }
            ],
            '13': [{
                    value: '01',
                    text: '01 - ARAMBALA'
                },
                {
                    value: '02',
                    text: '02 - CACAOPERA'
                },
                {
                    value: '03',
                    text: '03 - CORINTO'
                },
                {
                    value: '04',
                    text: '04 - CHILANGA'
                },
                {
                    value: '05',
                    text: '05 - DELIC DE CONCEP'
                },
                {
                    value: '06',
                    text: '06 - EL DIVISADERO'
                },
                {
                    value: '07',
                    text: '07 - EL ROSARIO'
                },
                {
                    value: '08',
                    text: '08 - GUALOCOCTI'
                },
                {
                    value: '09',
                    text: '09 - GUATAJIAGUA'
                },
                {
                    value: '10',
                    text: '10 - JOATECA'
                },
                {
                    value: '11',
                    text: '11 - JOCOAITIQUE'
                },
                {
                    value: '12',
                    text: '12 - JOCORO'
                },
                {
                    value: '13',
                    text: '13 - LOLOTIQUILLO'
                },
                {
                    value: '14',
                    text: '14 - MEANGUERA'
                },
                {
                    value: '15',
                    text: '15 - OSICALA'
                },
                {
                    value: '16',
                    text: '16 - PERQUÍN'
                },
                {
                    value: '17',
                    text: '17 - SAN CARLOS'
                },
                {
                    value: '18',
                    text: '18 - SAN FERNANDO'
                },
                {
                    value: '19',
                    text: '19 - SAN FCO GOTERA'
                },
                {
                    value: '20',
                    text: '20 - SAN ISIDRO'
                },
                {
                    value: '21',
                    text: '21 - SAN SIMÓN'
                },
                {
                    value: '22',
                    text: '22 - SENSEMBRA'
                },
                {
                    value: '23',
                    text: '23 - SOCIEDAD'
                },
                {
                    value: '24',
                    text: '24 - TOROLA'
                },
                {
                    value: '25',
                    text: '25 - YAMABAL'
                },
                {
                    value: '26',
                    text: '26 - YOLOAIQUÍN'
                }
            ],
            '14': [{
                    value: '01',
                    text: '01 - ANAMOROS'
                },
                {
                    value: '02',
                    text: '02 - BOLÍVAR'
                },
                {
                    value: '03',
                    text: '03 - CONCEP DE OTE'
                },
                {
                    value: '04',
                    text: '04 - CONCHAGUA'
                },
                {
                    value: '05',
                    text: '05 - EL CARMEN'
                },
                {
                    value: '06',
                    text: '06 - EL SAUCE'
                },
                {
                    value: '07',
                    text: '07 - INTIPUCÁ'
                },
                {
                    value: '08',
                    text: '08 - LA UNIÓN'
                },
                {
                    value: '09',
                    text: '09 - LISLIQUE'
                },
                {
                    value: '10',
                    text: '10 - MEANG DEL GOLFO'
                },
                {
                    value: '11',
                    text: '11 - NUEVA ESPARTA'
                },
                {
                    value: '12',
                    text: '12 - PASAQUINA'
                },
                {
                    value: '13',
                    text: '13 - POLORÓS'
                },
                {
                    value: '14',
                    text: '14 - SAN ALEJO'
                },
                {
                    value: '15',
                    text: '15 - SAN JOSE'
                },
                {
                    value: '16',
                    text: '16 - SANTA ROSA LIMA'
                },
                {
                    value: '17',
                    text: '17 - YAYANTIQUE'
                },
                {
                    value: '18',
                    text: '18 - YUCUAIQUÍN'
                }
            ]
        };

        document.getElementById('departamentoContribuyente').addEventListener('change', function() {
            const departamentoId = this.value;
            const municipioSelect = document.getElementById('municipioContribuyente');

            // Limpiar las opciones del select de municipios
            municipioSelect.innerHTML = '';

            // Añadir las nuevas opciones de municipios
            if (municipiosPorDepartamento[departamentoId]) {
                municipiosPorDepartamento[departamentoId].forEach(municipio => {
                    const option = document.createElement('option');
                    option.value = municipio.value;
                    option.textContent = municipio.text;
                    municipioSelect.appendChild(option);
                });
            }
        });

        // Trigger the change event on page load to populate the municipios for the selected departamento
        document.getElementById('departamentoContribuyente').dispatchEvent(new Event('change'));
    </script>
@endsection
