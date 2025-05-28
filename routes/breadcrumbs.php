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