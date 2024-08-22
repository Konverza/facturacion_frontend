@extends('layouts.app')
@include('business.layouts.navbar')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Productos</h1>
            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-4">
                <div class="card shadow bg-light card-body">
                    <h3>Añadir Producto</h3>
                    <form>
                        <div class="form-group mb-3">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" class="form-select">
                                <option selected>1 - Bien</option>
                                <option>2 - Servicio</option>
                                <option value="">3 - Bien y Servicio</option>
                                <option value="">4 - Otros Tributos</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="unidadMedida">Unidad de Medida:</label>
                            <select id="unidad" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <option value="01">01 - Metro</option>
                                <option value="02">02 - Yarda</option>
                                <option value="03">03 - Vara</option>
                                <option value="04">04 - Pie</option>
                                <option value="05">05 - Pulgada</option>
                                <option value="06">06 - Milímetro</option>
                                <option value="08">08 - Milla cuadrada</option>
                                <option value="09">09 - Kilómetro cuadrado</option>
                                <option value="10">10 - Hectárea</option>
                                <option value="11">11 - Manzana</option>
                                <option value="12">12 - Acre</option>
                                <option value="13">13 - Metro cuadrado</option>
                                <option value="14">14 - Yarda cuadrada</option>
                                <option value="15">15 - Vara cuadrada</option>
                                <option value="16">16 - Pie cuadrado</option>
                                <option value="17">17 - Pulgada cuadrada</option>
                                <option value="18">18 - Metro cúbico</option>
                                <option value="19">19 - Yarda cúbica</option>
                                <option value="20">20 - Barril</option>
                                <option value="21">21 - Pie cúbico</option>
                                <option value="22">22 - Galón</option>
                                <option value="23">23 - Litro</option>
                                <option value="24">24 - Botella</option>
                                <option value="25">25 - Pulgada cúbica</option>
                                <option value="26">26 - Mililitro</option>
                                <option value="27">27 - Onza fluida</option>
                                <option value="29">29 - Tonelada métrica</option>
                                <option value="30">30 - Tonelada</option>
                                <option value="31">31 - Quintal métrico</option>
                                <option value="32">32 - Quintal</option>
                                <option value="33">33 - Arroba</option>
                                <option value="34">34 - Kilogramo</option>
                                <option value="35">35 - Libra troy</option>
                                <option value="36">36 - Libra</option>
                                <option value="37">37 - Onza troy</option>
                                <option value="38">38 - Onza</option>
                                <option value="39">39 - Gramo</option>
                                <option value="40">40 - Miligramo</option>
                                <option value="42">42 - Megawatt</option>
                                <option value="43">43 - Kilowatt</option>
                                <option value="44">44 - Watt</option>
                                <option value="45">45 - Megavoltio-amperio</option>
                                <option value="46">46 - Kilovoltio-amperio</option>
                                <option value="47">47 - Voltio-amperio</option>
                                <option value="49">49 - Gigawatt-hora</option>
                                <option value="50">50 - Megawatt-hora</option>
                                <option value="51">51 - Kilowatt-hora</option>
                                <option value="52">52 - Watt-hora</option>
                                <option value="53">53 - Kilovoltio</option>
                                <option value="54">54 - Voltio</option>
                                <option value="55">55 - Millar</option>
                                <option value="56">56 - Medio millar</option>
                                <option value="57">57 - Ciento</option>
                                <option value="58">58 - Docena</option>
                                <option value="59">59 - Unidad</option>
                                <option value="99">99 - Otra</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="nombreProducto">Nombre del Producto:</label>
                            <input type="text" class="form-control" id="nombreProducto">
                        </div>

                        <div class="form-group mb-3">
                            <label for="precio">Precio:</label>
                            <input type="number" class="form-control" id="precio">
                        </div>

                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow bg-light card-body">
                    <p class="h4">Productos Registrados</p>
                </div>
            </div>
        </div>
    </div>
@endsection
