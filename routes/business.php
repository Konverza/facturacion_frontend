<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Business\AssociatedDocumentsController;
use App\Http\Controllers\Business\BulkController;
use App\Http\Controllers\Business\BusinessSucursalController;
use App\Http\Controllers\Business\CategoryController;
use App\Http\Controllers\Business\CuentasCobrarController;
use App\Http\Controllers\Business\CustomerContoller;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Business\DocumentController;
use App\Http\Controllers\Business\DTEController;
use App\Http\Controllers\Business\DTEDocumentsController;
use App\Http\Controllers\Business\DTEDonationController;
use App\Http\Controllers\Business\DTEProductController;
use App\Http\Controllers\Business\MailController;
use App\Http\Controllers\Business\MovementController;
use App\Http\Controllers\Business\PaymentMethodController;
use App\Http\Controllers\Business\PosController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\ProfileController;
use App\Http\Controllers\Business\RelatedDocumentsController;
use App\Http\Controllers\Business\ReportingController;
use App\Http\Controllers\Business\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:business", "web"])->prefix("business")->name("business.")->group(function () {
    Route::get("/select-business", [DashboardController::class, "business"])->name('select');
    Route::post("/select-business", [DashboardController::class, "selectBusiness"])->name('select-store');
    Route::get("/select-sucursal", [DashboardController::class, "sucursales"])->name('select-sucursal');
    Route::post("/select-sucursal", [DashboardController::class, "selectSucursal"])->name('select-sucursal-store');

    Route::get("/", [DashboardController::class, "index"])->name('index');
    Route::get("/dashboard", [DashboardController::class, "index"])->name('dashboard');
    Route::resource("/documents", DocumentController::class);
    Route::resource("/products", ProductController::class);
    Route::resource("/categories", CategoryController::class);
    Route::post("/products/add-stock", [ProductController::class, "add_stock"])->name('products.add-stock');
    Route::post("/products/remove-stock", [ProductController::class, "remove_stock"])->name('products.remove-stock');
    Route::post("/products/transfer-stock", [ProductController::class, "transferStock"])->name('products.transfer-stock');
    Route::get("/products/{id}/branch-info", [ProductController::class, "getBranchInfo"])->name('products.branch-info');
    Route::get("/products/{id}/branch-stock", [ProductController::class, "getBranchStock"])->name('products.branch-stock');
    Route::post("/products/import", [ProductController::class, "import"])->name('products.import');
    Route::resource("/customers", CustomerContoller::class);
    Route::post("/customers/import", [CustomerContoller::class, "import"])->name('customers.import');
    Route::resource("/movements", MovementController::class);
    Route::resource("/cuentas-por-cobrar", CuentasCobrarController::class);
    Route::post("/cuentas-por-cobrar/movement", [CuentasCobrarController::class, "movement"])->name('cuentas-por-cobrar.movement');

    Route::delete("/delete-dte/{id}", [DTEController::class, "delete"])->name('delete-dte');
    Route::delete("/delete-all-dte", [DTEController::class, "delete_all"])->name('delete-all-dte');

    // Reporting
    Route::get("/reporting", [ReportingController::class, "index"])->name('reporting.index');
    Route::post("/reporting", [ReportingController::class, "store"])->name('reporting.store');

    //Profile
    Route::get("/profile", [ProfileController::class, "index"])->name('profile.index');

    // POS
    Route::get("/pos", [PosController::class, "index"])->name('pos.index');

    // Bulk Emission
    Route::get("/bulk", [BulkController::class, "index"])->name('bulk.index');
    Route::delete("/bulk/{id}", [BulkController::class, "destroy"])->name('bulk.destroy');
    Route::get("/bulk/send", [BulkController::class, "send"])->name('bulk.send');
    Route::get("/bulk/template/{id}", [BulkController::class, "template"])->name('bulk.template');

    Route::prefix("dte")->name("dte.")->group(function () {
        Route::get("/generate", [DTEController::class, "create"])->name('create');
        Route::get("/cancel", [DTEController::class, "cancel"])->name('cancel');
        Route::post("/", [DTEController::class, "store"])->name('store');
        Route::post("/anular", [DTEController::class, "anular"])->name('anular');
        Route::post("/anexos", [DTEController::class, "anexos"])->name('anexos');
        Route::post("/send-email", [MailController::class, "send"])->name('send-email');
        Route::post("/send-whatsapp", [WhatsAppController::class, "send"])->name('send-whatsapp');
        Route::post("/download", [DocumentController::class, "zipAndDownload"])->name('download-dtes');
    // Nuevos endpoints JSON/Excel
    Route::post('/import-customers-excel', [DTEController::class, 'importCustomersExcel'])->name('import-customers-excel');
    Route::post('/submit-from-json', [DTEController::class, 'submitFromJson'])->name('submit-from-json');
    // Nuevo: importación combinada clientes+productos
    Route::post('/import-customers-products-excel', [DTEController::class, 'importCustomersProductsExcel'])->name('import-customers-products-excel');
    Route::post('/clear-bulk-session', [DTEController::class, 'clearSessionAfterBulk'])->name('clear-bulk-session');

        Route::prefix("product")->name("product.")->group(function () {
            //Products
            Route::post("/select", [DTEProductController::class, "select"])->name('select');
            Route::post("/store", [DTEProductController::class, "store"])->name('store');
            Route::post("/store-new", [DTEProductController::class, "store_new"])->name('store-new');
            Route::post("/store-pos", [DTEProductController::class, "store_from_pos"])->name('store-pos');
            Route::post("/unaffected-amounts", [DTEProductController::class, "unaffected_amounts"])->name('unaffected-amounts');
            Route::post("/taxes-iva", [DTEProductController::class, "taxes_iva"])->name('taxes-iva');
            Route::get("/delete/{id}", [DTEProductController::class, "delete"])->name('delete');
            Route::get("/withhold", [DTEProductController::class, "withhold"])->name('withhold');
            Route::post("/add-discounts", [DTEProductController::class, "add_discounts"])->name('add-discounts');
            Route::get("/remove-discounts", [DTEProductController::class, "remove_discounts"])->name('remove-discounts');
            Route::get("/exportacion", [DTEProductController::class, "exportacion"])->name('exportacion');
        });

        Route::prefix("associated-documents")->name("associated-documents.")->group(function () {
            //Associated documents
            Route::post("/store", [AssociatedDocumentsController::class, "store"])->name('store');
            Route::get("/delete/{id}", [AssociatedDocumentsController::class, "delete"])->name('delete');
            Route::get("/delete-doctor/{id}", [AssociatedDocumentsController::class, "delete_doctor"])->name('delete-doctor');
            Route::get("/delete-transport/{id}", [AssociatedDocumentsController::class, "delete_transport"])->name('delete-transport');
        });

        Route::prefix("related-documents")->name("related-documents.")->group(function () {
            //Related documents
            Route::get("/get", [RelatedDocumentsController::class, "get"])->name('get');
            Route::post("/store", [RelatedDocumentsController::class, "store"])->name('store');
            Route::get("/store-electric", [RelatedDocumentsController::class, "storeElectric"])->name('store-electric');
            Route::get("/delete/{id}", [RelatedDocumentsController::class, "delete"])->name('delete');
        });

        Route::prefix("documents")->name("documents.")->group(function () {
            Route::post("/store", [DTEDocumentsController::class, "store"])->name('store');
            Route::get("/delete/{id}", [DTEDocumentsController::class, "delete"])->name('delete');
            Route::get("/selected", [DTEDocumentsController::class, "selected"])->name('selected');
            Route::post("/query", [DTEDocumentsController::class, "queryDTE"])->name('queryDTE');
            Route::post("/store-electric", [DTEDocumentsController::class, "storeElectric"])->name('store-electric');
            Route::post("/store-hacienda", [DTEDocumentsController::class, "storeHacienda"])->name('store-hacienda');
        });

        Route::prefix("donation")->name("donation.")->group(function () {
            Route::post("/store", [DTEDonationController::class, "store"])->name('store');
            Route::get("/delete/{id}", [DTEDonationController::class, "delete"])->name('delete');
        });

        Route::prefix("payment-method")->name("payment-method.")->group(function () {
            Route::post("/store", [PaymentMethodController::class, "store"])->name('store');
            Route::get("/delete/{id}", [PaymentMethodController::class, "delete"])->name('delete');
        });

        //Routes store DTEs
        Route::post("/factura", [DTEController::class, "factura"])->name('factura');
        Route::post("/credito-fiscal", [DTEController::class, "credito_fiscal"])->name('credito-fiscal');
        Route::post("/nota-remision", [DTEController::class, "nota_remision"])->name('nota-remision');
        Route::post("/nota-credito", [DTEController::class, "nota_credito"])->name('nota-credito');
        Route::post("/nota-debito", [DTEController::class, "nota_debito"])->name('nota-debito');
        Route::post("/comprobante-retencion", [DTEController::class, "comprobante_retencion"])->name('comprobante-retencion');
        Route::post("/factura-exportacion", [DTEController::class, "factura_exportacion"])->name('factura-exportacion');
        Route::post("/factura-sujeto-excluido", [DTEController::class, "factura_sujeto_excluido"])->name('factura-sujeto-excluido');
        Route::post("/comprobante-donacion", [DTEController::class, "comprobante_donacion"])->name('comprobante-donacion');
    });

    Route::get("/get-session", function () {
        return session("dte");
    });

    Route::get("/get-municipios", [BusinessController::class, "getMunicipios"])->name("get-municipios");
    Route::put("/datos-empresa", [ProfileController::class, "datos_empresa"])->name("datos-empresa.update");

    // Sucursales
    Route::get("/{business_id}/sucursales", [BusinessSucursalController::class, "index"])->name(name: "sucursales.index");
    Route::post("/{business_id}/sucursales", [BusinessSucursalController::class, "store_sucursal"])->name("sucursales.store_sucursal");
    Route::get("/{business_id}/sucursales/{id}", [BusinessSucursalController::class, "edit"])->name("sucursales.edit");
    Route::put("/{business_id}/sucursales/{id}", [BusinessSucursalController::class, "update_sucursal"])->name("sucursales.update_sucursal");
    Route::delete("/{business_id}/sucursales/{id}", [BusinessSucursalController::class, "delete_sucursal"])->name("sucursales.delete_sucursal");
    // Punto de Venta
    Route::get("/{business_id}/sucursales/{sucursal_id}/puntos-venta", [BusinessSucursalController::class, "index_puntos_venta"])->name("puntos-venta.index");
    Route::get("/puntos-venta-html", [BusinessSucursalController::class, "getPuntosVenta"])->name("puntos-venta-html.index");
    Route::post("/{business_id}/sucursales/{sucursal_id}/puntos-venta", [BusinessSucursalController::class, "store_punto_venta"])->name("puntos-venta.store_punto_venta");
    Route::get("/{business_id}/sucursales/{sucursal_id}/puntos-venta/{id}", [BusinessSucursalController::class, "edit_punto_venta"])->name("puntos-venta.edit");
    Route::put("/{business_id}/sucursales/{sucursal_id}/puntos-venta/{id}", [BusinessSucursalController::class, "update_punto_venta"])->name("puntos-venta.update_punto_venta");
    Route::delete("/{business_id}/sucursales/{sucursal_id}/puntos-venta/{id}", [BusinessSucursalController::class, "delete_punto_venta"])->name("puntos-venta.delete_punto_venta");
    
});
