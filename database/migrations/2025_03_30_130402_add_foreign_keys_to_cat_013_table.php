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
        Schema::table('cat_013', function (Blueprint $table) {
            $table->foreign(['departamento'])->references(['codigo'])->on('cat_012')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_013', function (Blueprint $table) {
            $table->dropForeign('cat_013_departamento_foreign');
        });
    }
};
