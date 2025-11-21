<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadir campo has_customer_branches a la tabla business
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('has_customer_branches')->default(false)->after('show_special_prices');
        });

        // Añadir campo use_branches a la tabla business_customers
        Schema::table('business_customers', function (Blueprint $table) {
            $table->boolean('use_branches')->default(false)->after('special_price');
        });

        // Crear tabla business_customers_branches
        Schema::create('business_customers_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_customers_id')->index();
            $table->string('branch_code');
            $table->string('nombre');
            $table->string('departamento');
            $table->string('municipio');
            $table->text('complemento');
            $table->timestamps();

            $table->foreign('business_customers_id')
                  ->references('id')
                  ->on('business_customers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_customers_branches');
        
        Schema::table('business_customers', function (Blueprint $table) {
            $table->dropColumn('use_branches');
        });

        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('has_customer_branches');
        });
    }
};
