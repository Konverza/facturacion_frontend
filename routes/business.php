<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Business\AssociatedDocumentsController;
use App\Http\Controllers\Business\CuentasCobrarController;
use App\Http\Controllers\Business\CustomerContoller;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Business\DocumentController;
use App\Http\Controllers\Business\DTEController;
use App\Http\Controllers\Business\DTEDocumentsController;
use App\Http\Controllers\Business\DTEProductController;
use App\Http\Controllers\Business\MailController;
use App\Http\Controllers\Business\MovementController;
use App\Http\Controllers\Business\PaymentMethodController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\ProfileController;
use App\Http\Controllers\Business\RelatedDocumentsController;
use App\Http\Controllers\Business\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:business", "web"])->prefix("business")->name("business.")->group(function () {
    Route::get("/select-business", [DashboardController::class, "business"])->name('select');
    Route::post("/select-business", [DashboardController::class, "selectBusiness"])->name('select-store');

    Route::get("/", [DashboardController::class, "index"])->name('index');
    Route::get("/dashboard", [DashboardController::class, "index"])->name('dashboard');
    Route::resource("/documents", DocumentController::class);
    Route::resource("/products", ProductController::class);
    Route::post("/products/add-stock", [ProductController::class, "add_stock"])->name('products.add-stock');
    Route::resource("/customers", CustomerContoller::class);
    Route::resource("/movements", MovementController::class);
    Route::resource("/cuentas-por-cobrar", CuentasCobrarController::class);
    Route::post("/cuentas-por-cobrar/movement", [CuentasCobrarController::class, "movement"])->name('cuentas-por-cobrar.movement');

    Route::delete("/delete-dte/{id}", [DTEController::class, "delete"])->name('delete-dte');

    //Profile
    Route::get("/profile", [ProfileController::class, "index"])->name('profile.index');

    Route::prefix("dte")->name("dte.")->group(function () {
        Route::get("/generate", [DTEController::class, "create"])->name('create');
        Route::get("/cancel", [DTEController::class, "cancel"])->name('cancel');
        Route::post("/", [DTEController::class, "store"])->name('store');
        Route::post("/anular", [DTEController::class, "anular"])->name('anular');
        Route::post("/send-email", [MailController::class, "send"])->name('send-email');
        Route::post("/send-whatsapp", [WhatsAppController::class, "send"])->name('send-whatsapp');

        Route::prefix("product")->name("product.")->group(function () {
            //Products
            Route::post("/select", [DTEProductController::class, "select"])->name('select');
            Route::post("/store", [DTEProductController::class, "store"])->name('store');
            Route::post("/store-new", [DTEProductController::class, "store_new"])->name('store-new');
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
            Route::post("/store-electric", [DTEDocumentsController::class, "storeElectric"])->name('store-electric');
        });

        Route::prefix("payment-method")->name("payment-method.")->group(function () {
            Route::post("/store", [PaymentMethodController::class, "store"])->name('store');
            Route::get("/delete/{id}", [PaymentMethodController::class, "delete"])->name('delete');
        });

        //Routes store DTEs
        Route::post("/factura", [DTEController::class, "factura"])->name('factura');
        Route::post("/credito-fiscal", [DTEController::class, "credito_fiscal"])->name('credito-fiscal');
        Route::post("/nota-credito", [DTEController::class, "nota_credito"])->name('nota-credito');
        Route::post("/nota-debito", [DTEController::class, "nota_debito"])->name('nota-debito');
        Route::post("/comprobante-retencion", [DTEController::class, "comprobante_retencion"])->name('comprobante-retencion');
        Route::post("/factura-exportacion", [DTEController::class, "factura_exportacion"])->name('factura-exportacion');
        Route::post("/factura-sujeto-excluido", [DTEController::class, "factura_sujeto_excluido"])->name('factura-sujeto-excluido');
    });

    Route::get("/get-session", function () {
        return session("dte");
    });

    Route::get("/get-municipios", [BusinessController::class, "getMunicipios"])->name("get-municipios");
    Route::put("/datos-empresa", [ProfileController::class, "datos_empresa"])->name("datos-empresa.update");
});
