<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Imports\BusinessCustomerImport;
use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\BusinessCustomersBranch;
use App\Models\BusinessUser;
use App\Models\BusinessPriceVariant;
use App\Services\OctopusService;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class CustomerContoller extends Controller
{
    public $octopus_service;
    public $departamentos;
    public $tipos_documentos;
    public $actividades_economicas;
    public $countries;
    public $dte;

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->dte = session("dte", []);
    }

    public function index()
    {
        try {
            $business = Business::find(session("business"));
            $business_customers = BusinessCustomer::where("business_id", session("business"))->orderBy("id", "desc")->get();
            return view('business.customers.index', [
                'business_customers' => $business_customers,
                'business' => $business,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with([
                    "error" => "Error",
                    "error_message" => "Ha ocurrido un error al cargar los clientes"
                ]);
        }
    }

    public function create()
    {
        try {
            $departamentos = $this->octopus_service->getCatalog("CAT-012");
            $tipos_documentos = $this->octopus_service->getCatalog("CAT-022");
            $actividades_economicas = $this->octopus_service->getCatalog("CAT-019");
            $countries = $this->octopus_service->getCatalog("CAT-020");
            $priceVariants = BusinessPriceVariant::where('business_id', session('business'))
                ->orderBy('name')
                ->get();
            return view('business.customers.create', [
                'departamentos' => $departamentos,
                'tipos_documentos' => $tipos_documentos,
                'actividades_economicas' => $actividades_economicas,
                'countries' => $countries,
                'priceVariants' => $priceVariants,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with([
                    "error" => "Error",
                    "error_message" => "Ha ocurrido un error al cargar los clientes"
                ]);
        }
    }

    public function store(Request $request)
    {
        try {

            $numero_documento = $request->numero_documento;
            if ($request->tipo_documento === "36") {
                if (strlen($numero_documento) !== 14) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener exactamente 14 dígitos.'])->withInput();
                }
            } else if ($request->tipo_documento === "13") {
                if (!preg_match('/^\d{8}-\d{1}$/', $numero_documento)) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener el formato XXXXXXXX-X.'])->withInput();
                }
            }

            $business_user = BusinessUser::where("business_id", session("business"))
                ->where("user_id", auth()->user()->id)
                ->first();
            $business = Business::find(session('business'));
            $usePriceVariants = (bool) ($business?->price_variants_enabled);
            $priceVariantId = $usePriceVariants ? $request->input('price_variant_id') : null;

            if ($usePriceVariants && $priceVariantId) {
                $priceVariantId = BusinessPriceVariant::where('business_id', $business_user->business_id)
                    ->where('id', $priceVariantId)
                    ->value('id');
            } else {
                $priceVariantId = null;
            }
            DB::beginTransaction();

            $business_customer = new BusinessCustomer([
                "business_id" => $business_user->business_id,
                "tipoDocumento" => $request->tipo_documento,
                "numDocumento" => $request->numero_documento,
                "nrc" => str_replace('-', '', $request->nrc), // Elimina guiones del NRC
                "nombre" => $request->nombre,
                "codActividad" => $request->actividad_economica,
                "nombreComercial" => $request->nombre_comercial,
                "departamento" => $request->departamento,
                "municipio" => $request->municipio,
                "complemento" => $request->complemento,
                "telefono" => $request->telefono,
                "correo" => $request->correo,
                "codPais" => $request->codigo_pais,
                "tipoPersona" => $request->tipo_persona,
                "special_price" => $usePriceVariants ? false : ($request->has('special_price') ? true : false),
                "price_variant_id" => $priceVariantId,
                "use_branches" => $request->has('use_branches') ? true : false
            ]);

            $business_customer->save();

            // Guardar sucursales si existen
            if ($request->has('branches') && $request->has('use_branches')) {
                foreach ($request->branches as $branchData) {
                    $business_customer->branches()->create([
                        'branch_code' => $branchData['branch_code'],
                        'nombre' => $branchData['nombre'],
                        'departamento' => $branchData['departamento'],
                        'municipio' => $branchData['municipio'],
                        'complemento' => $branchData['complemento'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('business.customers.index')
                ->with("success", "Cliente guardado")
                ->with("success_message", "El cliente ha sido guardado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with("error", "Error")->with("error_message", "Ha ocurrido un error al guardar el cliente");
        }
    }

    public function destroy(string $id)
    {
        try {
            $business_customer = BusinessCustomer::where("id", $id)->first();
            if ($business_customer) {
                $business_customer->delete();
                return redirect()->route('business.customers.index')
                    ->with("success", "Cliente eliminado")
                    ->with("success_message", "El cliente ha sido eliminado correctamente");
            } else {
                return redirect()->route('business.customers.index')
                    ->with("error", "Error")
                    ->with("error_message", "El cliente no existe");
            }
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with("error", "Error")
                ->with("error_message", "Ha ocurrido un error al eliminar el cliente");
        }
    }

    public function edit(string $id)
    {
        try {
            $business_customer = BusinessCustomer::with('branches')->where("id", $id)->first();
            $municipios = $this->getMunicipios($business_customer->departamento);
            $priceVariants = BusinessPriceVariant::where('business_id', session('business'))
                ->orderBy('name')
                ->get();

            // Preparar municipios para cada sucursal
            $branchesMunicipios = [];
            foreach ($business_customer->branches as $branch) {
                $branchesMunicipios[$branch->id] = $this->getMunicipios($branch->departamento);
            }

            return view('business.customers.edit', [
                'customer' => $business_customer,
                'departamentos' => $this->octopus_service->getCatalog("CAT-012"),
                'tipos_documentos' => $this->octopus_service->getCatalog("CAT-022"),
                'actividades_economicas' => $this->octopus_service->getCatalog("CAT-019"),
                'countries' => $this->octopus_service->getCatalog("CAT-020"),
                'municipios' => $municipios,
                'branchesMunicipios' => $branchesMunicipios,
                'priceVariants' => $priceVariants,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with([
                    "error" => "Error",
                    "error_message" => "Ha ocurrido un error al cargar los clientes"
                ]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {

            $numero_documento = $request->numero_documento;
            if ($request->tipo_documento === "36") {
                if (strlen($numero_documento) !== 14) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener exactamente 14 dígitos.']);
                }
            } else if ($request->tipo_documento === "13") {
                if (!preg_match('/^\d{8}-\d{1}$/', $numero_documento)) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener el formato XXXXXXXX-X.']);
                }
            }

            $business_customer = BusinessCustomer::findOrFail($id);
            $business = Business::find(session('business'));
            $usePriceVariants = (bool) ($business?->price_variants_enabled);
            $priceVariantId = $usePriceVariants ? $request->input('price_variant_id') : null;

            if ($usePriceVariants && $priceVariantId) {
                $priceVariantId = BusinessPriceVariant::where('business_id', $business_customer->business_id)
                    ->where('id', $priceVariantId)
                    ->value('id');
            } else {
                $priceVariantId = null;
            }
            DB::beginTransaction();
            $business_customer->update([
                "tipoDocumento" => $request->tipo_documento,
                "numDocumento" => $request->numero_documento,
                "nrc" => str_replace('-', '', $request->nrc),
                "nombre" => $request->nombre,
                "codActividad" => $request->actividad_economica,
                "nombreComercial" => $request->nombre_comercial,
                "departamento" => $request->departamento,
                "municipio" => $request->municipio,
                "complemento" => $request->complemento,
                "telefono" => $request->telefono,
                "correo" => $request->correo,
                "codPais" => $request->codigo_pais,
                "tipoPersona" => $request->tipo_persona,
                "special_price" => $usePriceVariants ? false : ($request->has('special_price') ? true : false),
                "price_variant_id" => $priceVariantId,
                "use_branches" => $request->has('use_branches') ? true : false
            ]);

            // Manejar sucursales eliminadas
            if ($request->has('deleted_branches') && is_array($request->deleted_branches)) {
                $deletedCount = BusinessCustomersBranch::whereIn('id', $request->deleted_branches)
                    ->where('business_customers_id', $business_customer->id)
                    ->delete();
                Log::info("Sucursales eliminadas: {$deletedCount}", ['ids' => $request->deleted_branches]);
            }

            // Actualizar sucursales existentes
            if ($request->has('existing_branches')) {
                foreach ($request->existing_branches as $branchData) {
                    if (isset($branchData['id'])) {
                        $branch = \App\Models\BusinessCustomersBranch::find($branchData['id']);
                        if ($branch) {
                            $branch->update([
                                'branch_code' => $branchData['branch_code'],
                                'nombre' => $branchData['nombre'],
                                'departamento' => $branchData['departamento'],
                                'municipio' => $branchData['municipio'],
                                'complemento' => $branchData['complemento'],
                            ]);
                        }
                    }
                }
            }

            // Crear nuevas sucursales
            if ($request->has('branches') && $request->has('use_branches')) {
                foreach ($request->branches as $branchData) {
                    $business_customer->branches()->create([
                        'branch_code' => $branchData['branch_code'],
                        'nombre' => $branchData['nombre'],
                        'departamento' => $branchData['departamento'],
                        'municipio' => $branchData['municipio'],
                        'complemento' => $branchData['complemento'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('business.customers.index')->with([
                "success" => "Cliente actualizado",
                "success_message" => "El cliente ha sido actualizado correctamente"
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('business.customers.index')->with([
                "error" => "Error",
                "error_message" => "El cliente no existe"
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Si falla, revierte cambios
            return redirect()->route('business.customers.index')->with([
                "error" => "Error",
                "error_message" => "Ha ocurrido un error al actualizar el cliente"
            ]);
        }
    }

    public function show(string $id)
    {
        try {
            $business_customer = BusinessCustomer::where("id", $id)->with('branches')->first();
            session()->put("dte", array_merge(session("dte", []), [
                "customer" => $business_customer
            ]));

            if ($this->dte["type"] === "03" || $this->dte["type"] === "05" || $this->dte["type"] === "06") {
                $business_customer->numDocumento = str_replace("-", "", $business_customer->numDocumento);
            }

            $response = [
                "customer" => $business_customer,
                "select_tipos_documentos" => view("layouts.partials.ajax.business.select-tipos-documentos", [
                    "tipo_documento" => $business_customer->tipoDocumento,
                    "tipos_documentos" => $this->octopus_service->getCatalog("CAT-022")
                ])->render(),
                "select_actividad_economica" => view("layouts.partials.ajax.business.select-actividad-economica", [
                    "actividad_economica" => $business_customer->codActividad,
                    "actividades_economicas" => $this->octopus_service->getCatalog("CAT-019", null, true, true)
                ])->render(),
                "select_departamentos" => view("layouts.partials.ajax.business.select-departamentos", [
                    "departamento" => $business_customer->departamento,
                    "departamentos" => $this->octopus_service->getCatalog("CAT-012")
                ])->render(),
                "select_municipios" => view("layouts.partials.ajax.admin.select-municipios", [
                    "municipio" => $business_customer->municipio,
                    "municipios" => $this->getMunicipios($business_customer->departamento)
                ])->render(),
                "select_countries" => view("layouts.partials.ajax.business.select-countries", [
                    "country" => $business_customer->codPais,
                    "countries" => $this->octopus_service->getCatalog("CAT-020")
                ])->render(),
                "select_tipo_persona" => view("layouts.partials.ajax.business.select-tipos-personas", [
                    "tipo_persona" => $business_customer->tipoPersona
                ])->render()
            ];

            // Si el cliente tiene sucursales habilitadas y tiene sucursales
            if ($business_customer->use_branches && $business_customer->branches->isNotEmpty()) {
                $response['has_branches'] = true;
                $response['branches'] = $business_customer->branches;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Error",
                "error_message" => "Ha ocurrido un error al cargar el cliente"
            ]);
        }
    }

    public function getMunicipios($departamento)
    {
        try {
            $municipios = $this->octopus_service->getCatalog("CAT-012", $departamento);
            return $municipios;
        } catch (\Exception $e) {
            Log::error("Error obteniendo municipios: " . $e->getMessage());
            return [];
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv'
            ]);
            $departamentos = $this->octopus_service->simpleDepartamentos();
            $tipos_documentos = $this->octopus_service->getCatalog("CAT-022");
            $tipos_personas = [
                '1' => 'Natural',
                '2' => 'Juridica',
            ];
            $actividades_economicas = $this->octopus_service->getCatalog("CAT-019");
            $countries = $this->octopus_service->getCatalog("CAT-020");
            $business_id = session("business");
            Excel::import(new BusinessCustomerImport(
                $business_id,
                $tipos_documentos,
                $tipos_personas,
                $actividades_economicas,
                $countries,
                $departamentos
            ), $request->file('file'));
            // $business_id = session("business");
            // $unidades_medidas = $this->unidades_medidas;
            // Excel::import(new BusinessProductImport($business_id, $unidades_medidas), $request->file('file'));

            return redirect()->route('business.customers.index')
                ->with('success', 'Clientes importados')
                ->with("success_message", "Los clientes han sido importados correctamente");
        } catch (\Exception $e) {
            Log::error('Error al importar clientes: ' . $e->getMessage());
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al importar los clientes");
        }
    }

    /**
     * Genera la imagen del QR para registro de clientes
     */
    public function generateQRImage()
    {
        try {
            $business = Business::find(session("business"));

            if (!$business) {
                abort(404, 'Negocio no encontrado');
            }

            // URL de registro
            $registrationUrl = route('registro-clientes', ['nit' => $business->nit]);

            // Cache key único para cada negocio
            $cacheKey = 'qr_image_' . $business->id;

            // Verificar si existe en caché
            $cachedImage = Cache::remember($cacheKey, now()->addDays(30), function () use ($business, $registrationUrl) {
                // Dimensiones de la imagen
                $width = 1080;
                $height = 1080;

                // Cargar imagen de fondo
                $backgroundPath = public_path('images/fondo_qr.jpeg');
                $image = false;

                if (file_exists($backgroundPath)) {
                    $imageInfo = @getimagesize($backgroundPath);
                    if ($imageInfo !== false) {
                        switch ($imageInfo[2]) {
                            case IMAGETYPE_JPEG:
                                $image = imagecreatefromjpeg($backgroundPath);
                                break;
                            case IMAGETYPE_PNG:
                                $image = imagecreatefrompng($backgroundPath);
                                break;
                        }
                    }
                }

                // Si no se pudo cargar el fondo, crear imagen en blanco
                if ($image === false) {
                    $image = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($image, 255, 255, 255);
                    imagefill($image, 0, 0, $white);
                }

                // Redimensionar la imagen de fondo a las dimensiones deseadas
                $image = imagescale($image, $width, $height);

                // Colores
                $white = imagecolorallocate($image, 255, 255, 255);
                $darkBlue = imagecolorallocate($image, 36, 31, 97); // #241F61

                // Copiar fuentes a ubicación temporal sin caracteres especiales (para GD en Windows)
                $fontPathBold = $this->copyFontToTemp(storage_path('app/fonts/font-bold.ttf'));
                $fontPathRegular = $this->copyFontToTemp(storage_path('app/fonts/font-regular.ttf'));

                $useTTF = $fontPathBold !== null && $fontPathRegular !== null;

                // Texto superior "Registra tus datos" con fondo oscuro
                $headerText = "Registra tus datos";
                $fontSize = 48; // Tamaño de fuente grande y legible
                $headerHeight = 100; // Altura suficiente para el texto
                $headerY = 60;
                $headerMargin = 100; // Margen lateral

                // Dibujar rectángulo con esquinas redondeadas para el fondo del texto
                $this->imagefilledroundedrectangle($image, $headerMargin, $headerY, $width - $headerMargin, $headerY + $headerHeight, 15, $darkBlue);

                // Dibujar texto centrado
                if ($useTTF) {
                    try {
                        $bbox = imagettfbbox($fontSize, 0, $fontPathBold, $headerText);
                        $textWidth = abs($bbox[4] - $bbox[0]);
                        $textHeight = abs($bbox[5] - $bbox[1]);
                        $x = ($width - $textWidth) / 2;
                        $y = $headerY + ($headerHeight / 2) + ($textHeight / 2);
                        imagettftext($image, $fontSize, 0, $x, $y, $white, $fontPathBold, $headerText);
                    } catch (\Exception $e) {
                        Log::error('Error with TTF header: ' . $e->getMessage());
                        // Fallback a fuente del sistema
                        imagestring($image, 5, ($width - (imagefontwidth(5) * strlen($headerText))) / 2, $headerY + 40, $headerText, $white);
                    }
                } else {
                    Log::warning('TTF fonts not found, using system font');
                    imagestring($image, 5, ($width - (imagefontwidth(5) * strlen($headerText))) / 2, $headerY + 40, $headerText, $white);
                }

                // Texto descriptivo debajo del encabezado
                $descriptionY = $headerY + $headerHeight + 40;
                $descriptionLines = [
                    "Dando cumplimiento a las disposiciones del Ministerio de Hacienda,",
                    "estamos implementando la facturación electrónica.",
                    "Escanee el siguiente código QR y llene formulario con los datos correspondientes"
                ];

                $descriptionFontSize = 20;
                $lineHeight = 32;

                // Dibujar texto centrado
                if ($useTTF) {
                    foreach ($descriptionLines as $line) {
                        $bbox = imagettfbbox($descriptionFontSize, 0, $fontPathRegular, $line);
                        $textWidth = abs($bbox[4] - $bbox[0]);
                        $x = ($width - $textWidth) / 2;
                        imagettftext($image, $descriptionFontSize, 0, $x, $descriptionY, $white, $fontPathRegular, $line);
                        $descriptionY += $lineHeight;
                    }
                } else {
                    foreach ($descriptionLines as $line) {
                        imagestring($image, 4, ($width - (imagefontwidth(4) * strlen($line))) / 2, $descriptionY, $line, $white);
                        $descriptionY += $lineHeight;
                    }
                }

                $descriptionY += 30; // Espacio adicional antes del QR

                // Generar QR blanco
                $tempQrPath = storage_path('app/temp_qr_' . $business->id . '.png');
                $qrSize = 500;

                try {
                    // Generar QR usando la matriz de datos
                    $qrCode = \BaconQrCode\Encoder\Encoder::encode(
                        $registrationUrl,
                        \BaconQrCode\Common\ErrorCorrectionLevel::H(),
                        'UTF-8'
                    );

                    $matrix = $qrCode->getMatrix();
                    $matrixWidth = $matrix->getWidth();

                    // Calcular el tamaño de cada módulo del QR
                    $margin = 30;
                    $availableSize = $qrSize - (2 * $margin);
                    $moduleSize = floor($availableSize / $matrixWidth);
                    $actualQrSize = ($moduleSize * $matrixWidth) + (2 * $margin);

                    // Crear imagen del QR con fondo transparente
                    $qrImg = imagecreatetruecolor($actualQrSize, $actualQrSize);

                    // Hacer el fondo transparente
                    imagesavealpha($qrImg, true);
                    $transparent = imagecolorallocatealpha($qrImg, 0, 0, 0, 127);
                    imagefill($qrImg, 0, 0, $transparent);

                    $whiteColor = imagecolorallocate($qrImg, 255, 255, 255);

                    // Dibujar el QR módulo por módulo en blanco
                    for ($y = 0; $y < $matrixWidth; $y++) {
                        for ($x = 0; $x < $matrixWidth; $x++) {
                            if ($matrix->get($x, $y) === 1) {
                                $posX = $margin + ($x * $moduleSize);
                                $posY = $margin + ($y * $moduleSize);
                                imagefilledrectangle(
                                    $qrImg,
                                    $posX,
                                    $posY,
                                    $posX + $moduleSize - 1,
                                    $posY + $moduleSize - 1,
                                    $whiteColor
                                );
                            }
                        }
                    }

                    imagepng($qrImg, $tempQrPath);
                    imagedestroy($qrImg);
                } catch (\Exception $e) {
                    Log::warning('Error generando QR: ' . $e->getMessage());
                }

                // Posicionar el QR después del texto descriptivo
                if (file_exists($tempQrPath)) {
                    $qr = imagecreatefrompng($tempQrPath);
                    $qrWidth = imagesx($qr);
                    $qrHeight = imagesy($qr);
                    $qrX = ($width - $qrWidth) / 2;
                    $qrY = $descriptionY;

                    // Copiar QR con transparencia
                    imagecopy($image, $qr, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);
                    imagedestroy($qr);
                }

                @unlink($tempQrPath);

                // Esquina inferior izquierda: Nombre del negocio
                $businessName = $business->nombre ?? "";
                $bottomMargin = 60;
                $leftMargin = 50;

                if ($useTTF) {
                    $businessFontSize = 24; // Tamaño más grande para el nombre del negocio
                    imagettftext($image, $businessFontSize, 0, $leftMargin, $height - $bottomMargin, $white, $fontPathBold, $businessName);
                } else {
                    imagestring($image, 4, $leftMargin, $height - $bottomMargin - 10, $businessName, $white);
                }

                // Esquina inferior derecha: "Con la tecnología de" + logo
                $footerText = "Con la tecnología de";
                $rightMargin = 50;
                $logoSize = 50; // Logo más grande

                // Cargar logo
                $logoPath = public_path('images/only-icon.png');
                $logoWidth = 0;

                if (file_exists($logoPath)) {
                    try {
                        $imageInfo = @getimagesize($logoPath);
                        if ($imageInfo !== false) {
                            $logo = false;

                            switch ($imageInfo[2]) {
                                case IMAGETYPE_PNG:
                                    $logo = @imagecreatefrompng($logoPath);
                                    break;
                                case IMAGETYPE_JPEG:
                                    $logo = @imagecreatefromjpeg($logoPath);
                                    break;
                            }

                            if ($logo !== false) {
                                // Mantener proporciones del logo
                                $originalWidth = imagesx($logo);
                                $originalHeight = imagesy($logo);
                                $logoHeight = $logoSize;
                                $logoWidth = ($originalWidth * $logoHeight) / $originalHeight;

                                $logo = imagescale($logo, $logoWidth, $logoHeight);

                                // Posicionar logo en la esquina inferior derecha
                                $logoX = $width - $rightMargin - $logoWidth;
                                $logoY = $height - $bottomMargin - $logoHeight;

                                imagecopy($image, $logo, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);
                                imagedestroy($logo);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::debug('No se pudo cargar el logo: ' . $e->getMessage());
                    }
                }

                // Texto "Con la tecnología de" sobre el logo
                if ($useTTF) {
                    $footerFontSize = 18; // Tamaño más grande para el footer
                    $bbox = imagettfbbox($footerFontSize, 0, $fontPathRegular, $footerText);
                    $textWidth = abs($bbox[4] - $bbox[0]);
                    $textX = $width - $rightMargin - $logoWidth - 15 - $textWidth;
                    $textY = $height - $bottomMargin - ($logoSize / 2) + 7;
                    imagettftext($image, $footerFontSize, 0, $textX, $textY, $white, $fontPathRegular, $footerText);
                } else {
                    $textX = $width - $rightMargin - $logoWidth - 200;
                    imagestring($image, 3, $textX, $height - $bottomMargin - 15, $footerText, $white);
                }

                // Convertir a PNG y guardar en caché
                ob_start();
                imagepng($image);
                $imageData = ob_get_clean();
                imagedestroy($image);

                return base64_encode($imageData);
            });

            // Decodificar y devolver la imagen
            $imageData = base64_decode($cachedImage);

            return response($imageData)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'inline; filename="QR-Registro-Clientes.png"');

        } catch (\Exception $e) {
            Log::error('Error generando QR: ' . $e->getMessage());
            abort(500, 'Error generando el código QR');
        }
    }

    /**
     * Muestra el formulario público de registro de clientes
     */
    public function showPublicRegistration($nit)
    {
        try {
            // Buscar el negocio por NIT
            $business = Business::where('nit', $nit)->first();

            if (!$business) {
                return view('registro-clientes', [
                    'business' => null,
                    'error' => 'El NIT proporcionado no es válido o no está registrado en nuestro sistema.'
                ]);
            }

            // Obtener catálogos necesarios
            $departamentos = $this->octopus_service->getCatalog("CAT-012");
            $tipos_documentos = $this->octopus_service->getCatalog("CAT-022");
            $actividades_economicas = $this->octopus_service->getCatalog("CAT-019");
            $countries = $this->octopus_service->getCatalog("CAT-020");

            return view('registro-clientes', [
                'business' => $business,
                'departamentos' => $departamentos,
                'tipos_documentos' => $tipos_documentos,
                'actividades_economicas' => $actividades_economicas,
                'countries' => $countries,
                'error' => null
            ]);

        } catch (\Exception $e) {
            Log::error('Error mostrando formulario público: ' . $e->getMessage());
            return view('registro-clientes', [
                'business' => null,
                'error' => 'Ha ocurrido un error al cargar el formulario. Por favor, intente nuevamente.'
            ]);
        }
    }

    /**
     * Procesa el registro público de un cliente
     */
    public function storePublicRegistration(Request $request, $nit)
    {
        // Buscar el negocio primero
        $business = Business::where('nit', $nit)->first();

        if (!$business) {
            return redirect()->route('registro-clientes', ['nit' => $nit])
                ->with('error', 'El NIT proporcionado no es válido.');
        }

        // Validar datos del formulario
        $validated = $request->validate([
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string',
            'nrc' => 'required_with:actividad_economica|nullable|string',
            'nombre' => 'required|string|max:255',
            'actividad_economica' => 'required_with:nrc|nullable|string',
            'nombre_comercial' => 'nullable|string|max:255',
            'departamento' => 'required|string',
            'municipio' => 'required|string',
            'complemento' => 'required|string|max:500',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'codigo_pais' => 'nullable|string',
            'tipo_persona' => 'nullable|string',
        ], [
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'nrc.required_with' => 'El NRC es obligatorio cuando se proporciona una actividad económica.',
            'nombre.required' => 'El nombre es obligatorio.',
            'actividad_economica.required_with' => 'La actividad económica es obligatoria cuando se proporciona un NRC.',
            'departamento.required' => 'El departamento es obligatorio.',
            'municipio.required' => 'El municipio es obligatorio.',
            'complemento.required' => 'La dirección es obligatoria.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser válido.',
        ]);

        // Validar número de documento según tipo
        $numero_documento = $validated['numero_documento'];
        if ($validated['tipo_documento'] === "36") {
            if (strlen($numero_documento) !== 14) {
                return redirect()->back()->withInput()
                    ->withErrors(['numero_documento' => 'El NIT debe tener 14 dígitos']);
            }
        } else if ($validated['tipo_documento'] === "13") {
            if (!preg_match('/^\d{8}-\d{1}$/', $numero_documento)) {
                return redirect()->back()->withInput()
                    ->withErrors(['numero_documento' => 'El DUI debe tener el formato correcto (########-#)']);
            }
        }

        try {
            DB::beginTransaction();

            // Verificar si el cliente ya existe
            $existingCustomer = BusinessCustomer::where('business_id', $business->id)
                ->where('numDocumento', $numero_documento)
                ->first();

            if ($existingCustomer) {
                // Actualizar cliente existente
                $updated = $existingCustomer->update([
                    "tipoDocumento" => $validated['tipo_documento'],
                    "numDocumento" => $validated['numero_documento'],
                    "nrc" => str_replace('-', '', $validated['nrc'] ?? ''),
                    "nombre" => $validated['nombre'],
                    "codActividad" => $validated['actividad_economica'],
                    "nombreComercial" => $validated['nombre_comercial'],
                    "departamento" => $validated['departamento'],
                    "municipio" => $validated['municipio'],
                    "complemento" => $validated['complemento'],
                    "telefono" => $validated['telefono'],
                    "correo" => $validated['correo'],
                    "codPais" => $validated['codigo_pais'],
                    "tipoPersona" => $validated['tipo_persona'],
                ]);

                if (!$updated) {
                    throw new \Exception('No se pudieron actualizar los datos');
                }

                DB::commit();

                return redirect()->route('registro-clientes', ['nit' => $nit])
                    ->with('success', 'Sus datos han sido actualizados correctamente. ¡Gracias!');
            }

            // Crear nuevo cliente
            $business_customer = new BusinessCustomer([
                "business_id" => $business->id,
                "tipoDocumento" => $validated['tipo_documento'],
                "numDocumento" => $validated['numero_documento'],
                "nrc" => str_replace('-', '', $validated['nrc'] ?? ''),
                "nombre" => $validated['nombre'],
                "codActividad" => $validated['actividad_economica'],
                "nombreComercial" => $validated['nombre_comercial'],
                "departamento" => $validated['departamento'],
                "municipio" => $validated['municipio'],
                "complemento" => $validated['complemento'],
                "telefono" => $validated['telefono'],
                "correo" => $validated['correo'],
                "codPais" => $validated['codigo_pais'],
                "tipoPersona" => $validated['tipo_persona'],
                "special_price" => false,
                "use_branches" => false
            ]);

            if (!$business_customer->save()) {
                throw new \Exception('No se pudieron guardar los datos');
            }

            DB::commit();

            return redirect()->route('registro-clientes', ['nit' => $nit])
                ->with('success', 'Sus datos han sido registrados correctamente. ¡Gracias!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en registro público: ' . $e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', 'Ha ocurrido un error al registrar sus datos. Por favor, intente nuevamente.');
        }
    }

    /**
     * Dibuja un rectángulo con esquinas redondeadas
     */
    private function imagefilledroundedrectangle($image, $x1, $y1, $x2, $y2, $radius, $color)
    {
        // Dibujar el rectángulo principal
        imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        // Dibujar las esquinas redondeadas
        imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    /**
     * Copia una fuente a una ubicación temporal sin caracteres especiales
     * Esto resuelve el problema de GD que no puede leer fuentes en rutas con tildes en Windows
     */
    private function copyFontToTemp($sourcePath)
    {
        if (!file_exists($sourcePath)) {
            Log::warning('Font file does not exist: ' . $sourcePath);
            return null;
        }

        // En Linux/Mac, devolver la ruta original (no hay problema con caracteres especiales)
        if (DIRECTORY_SEPARATOR !== '\\') {
            return $sourcePath;
        }

        try {
            // Usar sys_get_temp_dir() que normalmente es C:\Windows\Temp o C:\Users\...\AppData\Local\Temp
            $tempDir = sys_get_temp_dir();
            $fileName = 'laravel_font_' . md5($sourcePath) . '_' . basename($sourcePath);
            $tempPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

            // Si ya existe y es más nuevo que el origen, usarlo
            if (file_exists($tempPath) && filemtime($tempPath) >= filemtime($sourcePath)) {
                return $tempPath;
            }

            // Copiar la fuente a temp
            if (copy($sourcePath, $tempPath)) {
                return $tempPath;
            }

            Log::error('Could not copy font to temp', ['source' => $sourcePath, 'dest' => $tempPath]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error copying font to temp: ' . $e->getMessage());
            return null;
        }
    }
}
