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
        Schema::table('tributes', function (Blueprint $table) {
            $table->boolean("aplicar_a_cantidad")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tributes', function (Blueprint $table) {
            $table->dropColumn("aplicar_a_cantidad");
        });
    }
};
