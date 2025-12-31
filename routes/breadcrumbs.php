<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Blade;

//ADMIN

Breadcrumbs::for("admin", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='home' class='w-4 h-4'/>");
    $trail->push($icon . "Admin", route("admin.dashboard"));
});

Breadcrumbs::for("admin.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='home' class='w-4 h-4'/>");
    $trail->push($icon . "Admin", route("admin.dashboard"));
});

Breadcrumbs::for("admin.dashboard", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='dashboard' class='w-4 h-4'/>");
    $trail->parent("admin");
    $trail->push($icon . "Dashboard", route("admin.dashboard"));
});

//Business
Breadcrumbs::for("admin.business.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("admin");
    $trail->push($icon . "Negocios", route("admin.business.index"));
});

Breadcrumbs::for("admin.business.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("admin.business.index");
    $trail->push($icon . "Nuevo", route("admin.business.create"));
});

Breadcrumbs::for("admin.business.edit", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='pencil' class='w-4 h-4'/>");
    $trail->parent("admin.business.index");
    $trail->push($icon . "Editar", route("admin.business.edit", $id));
});

Breadcrumbs::for("admin.business.rebuild-stock", function (BreadcrumbTrail $trail, $business) {
    $icon = Blade::render("<x-icon icon='refresh' class='w-4 h-4'/>");
    $trail->parent("admin.business.index");
    $trail->push($icon . "Reconstruir Stock", route("admin.business.rebuild-stock", $business));
});

Breadcrumbs::for("admin.sucursales.index", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("admin.business.index");
    $trail->push($icon . "Sucursales", route("admin.sucursales.index", $id));
});

Breadcrumbs::for("admin.puntos-venta.index", function (BreadcrumbTrail $trail, string $business_id, string $sucursal_id) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("admin.sucursales.index", $business_id);
    $trail->push($icon . "Puntos de Venta", route("admin.puntos-venta.index", [$business_id, $sucursal_id]));
});

//Plans
Breadcrumbs::for("admin.plans.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='credit-card' class='w-4 h-4'/>");
    $trail->parent("admin");
    $trail->push($icon . "Planes", route("admin.plans.index"));
});

//Users
Breadcrumbs::for("admin.users.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='users' class='w-4 h-4'/>");
    $trail->parent("admin");
    $trail->push($icon . "Usuarios", route("admin.users.index"));
});

Breadcrumbs::for("admin.users.businesses", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("admin.users.index");
    $trail->push($icon . "Negocios Asociados", route("admin.users.index"));
});

// Ads
Breadcrumbs::for("admin.ads.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='ad' class='w-4 h-4'/>");
    $trail->parent("admin");
    $trail->push($icon . "Anuncios", route("admin.ads.index"));
});
Breadcrumbs::for("admin.ads.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("admin.ads.index");
    $trail->push($icon . "Nuevo Anuncio", route("admin.ads.create"));
});
Breadcrumbs::for("admin.ads.edit", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='pencil' class='w-4 h-4'/>");
    $trail->parent("admin.ads.index");
    $trail->push($icon . "Editar Anuncio", route("admin.ads.edit", $id));
});

// Notifications
Breadcrumbs::for("admin.notifications.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='bell' class='w-4 h-4'/>");
    $trail->parent("admin");
    $trail->push($icon . "Notificaciones", route("admin.notifications.index"));
});

Breadcrumbs::for("admin.notifications.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='mail-star' class='w-4 h-4'/>");
    $trail->parent("admin.notifications.index");
    $trail->push($icon . "Nueva Notificación", route("admin.notifications.create"));
});


//BUSINESS

Breadcrumbs::for("business", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='home' class='w-4 h-4'/>");
    $trail->push($icon . "Negocio", route("business.dashboard"));
});

Breadcrumbs::for("business.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='home' class='w-4 h-4'/>");
    $trail->push($icon . "Negocio", route("business.dashboard"));
});

Breadcrumbs::for("business.dashboard", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='dashboard' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Dashboard", route("business.dashboard"));
});

//Documents 
Breadcrumbs::for("business.documents.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='document' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Documentos", route("business.documents.index"));
});

//Products
Breadcrumbs::for("business.products.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='box' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Productos", route("business.products.index"));
});

//Products > Create
Breadcrumbs::for("business.products.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business.products.index");
    $trail->push($icon . "Nuevo", route("business.products.create"));
});

//Products > Edit   
Breadcrumbs::for("business.products.edit", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='pencil' class='w-4 h-4'/>");
    $trail->parent("business.products.index");
    $trail->push($icon . "Editar", route("business.products.edit", $id));
});

//Categories
Breadcrumbs::for("business.categories.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='tag' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Categorias", route("business.categories.index"));
});
//Categories > Create
Breadcrumbs::for("business.categories.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business.categories.index");
    $trail->push($icon . "Nueva", route("business.categories.create"));
});
//Categories > Edit
Breadcrumbs::for("business.categories.edit", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='pencil' class='w-4 h-4'/>");
    $trail->parent("business.categories.index");
    $trail->push($icon . "Editar", route("business.categories.edit", $id));
});


//Customers
Breadcrumbs::for("business.customers.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='users' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Clientes", route("business.customers.index"));
});

//Customers > Create
Breadcrumbs::for("business.customers.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business.customers.index");
    $trail->push($icon . "Nuevo", route("business.customers.create"));
});

//Customers > Edit
Breadcrumbs::for("business.customers.edit", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='pencil' class='w-4 h-4'/>");
    $trail->parent("business.customers.index");
    $trail->push($icon . "Editar", route("business.customers.edit", $id));
});

//DTE
Breadcrumbs::for("business.dte.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Nuevo DTE", route("business.dte.create"));
});

//Cuentas por cobrar
Breadcrumbs::for("business.cuentas-por-cobrar.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='credit-card' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Cuentas por cobrar", route("business.cuentas-por-cobrar.index"));
});

//Perfil
Breadcrumbs::for("business.profile.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='user' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Perfil", route("business.profile.index"));
});

//Movements
Breadcrumbs::for("business.movements.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='cash' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Movimientos", route("business.movements.index"));
});

// POS
Breadcrumbs::for("business.pos.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='cash-register' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "POS", route("business.pos.index"));
});

Breadcrumbs::for("business.sucursales.index", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Sucursales", route("business.sucursales.index", $id));
});

Breadcrumbs::for("business.puntos-venta.index", function (BreadcrumbTrail $trail, string $business_id, string $sucursal_id) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("business.sucursales.index", $business_id);
    $trail->push($icon . "Puntos de Venta", route("business.puntos-venta.index", [$business_id, $sucursal_id]));
});

// Inventario POS
Breadcrumbs::for("business.inventory.pos.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='box' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Inventario por POS", route("business.inventory.pos.index"));
});

Breadcrumbs::for("business.inventory.pos.show", function (BreadcrumbTrail $trail, string $puntoVentaId) {
    $icon = Blade::render("<x-icon icon='building-store' class='w-4 h-4'/>");
    $trail->parent("business.inventory.pos.index");
    $trail->push($icon . "Stock de POS", route("business.inventory.pos.show", $puntoVentaId));
});

Breadcrumbs::for("business.inventory.pos.assign", function (BreadcrumbTrail $trail, string $puntoVentaId) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business.inventory.pos.index");
    $trail->push($icon . "Asignar Productos", route("business.inventory.pos.assign", $puntoVentaId));
});

Breadcrumbs::for("business.inventory.pos-transfers.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='transfer' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Traslados", route("business.inventory.pos-transfers.index"));
});

Breadcrumbs::for("business.inventory.transfers.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='transfer' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Traslados", route("business.inventory.transfers.index"));
});

Breadcrumbs::for("business.inventory.pos-transfers.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business.inventory.pos-transfers.index");
    $trail->push($icon . "Nuevo Traslado", route("business.inventory.pos-transfers.create"));
});

Breadcrumbs::for("business.inventory.transfers.create", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='plus' class='w-4 h-4'/>");
    $trail->parent("business.inventory.transfers.index");
    $trail->push($icon . "Nuevo Traslado", route("business.inventory.transfers.create"));
});

Breadcrumbs::for("business.inventory.pos-transfers.show", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='document' class='w-4 h-4'/>");
    $trail->parent("business.inventory.pos-transfers.index");
    $trail->push($icon . "Detalle de Traslado", route("business.inventory.pos-transfers.show", $id));
});

Breadcrumbs::for("business.inventory.transfers.show", function (BreadcrumbTrail $trail, string $id) {
    $icon = Blade::render("<x-icon icon='document' class='w-4 h-4'/>");
    $trail->parent("business.inventory.transfers.index");
    $trail->push($icon . "Detalle de Traslado", route("business.inventory.transfers.show", $id));
});

// Reportería
Breadcrumbs::for("business.reporting.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='file-report' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Reportería", route("business.reporting.index"));
});

// Bulk Emission
Breadcrumbs::for("business.bulk.index", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='files' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Plantillas de DTEs", route("business.bulk.index"));
});

Breadcrumbs::for("business.bulk.send", function (BreadcrumbTrail $trail) {
    $icon = Blade::render("<x-icon icon='paper-plane' class='w-4 h-4'/>");
    $trail->parent("business");
    $trail->push($icon . "Envío Masivo", route("business.bulk.send"));
});