<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_001', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 20)->notNullable();
        });

        Schema::create('cat_002', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 50)->notNullable();
        });

        Schema::create('cat_003', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 30)->notNullable();
        });

        Schema::create('cat_004', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 30)->notNullable();
        });

        Schema::create('cat_005', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_006', function (Blueprint $table) {
            $table->string('codigo', 3)->notNullable();
            $table->string('valores', 50)->notNullable();
        });

        Schema::create('cat_007', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 20)->notNullable();
        });

        Schema::create('cat_009', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 30)->notNullable();
        });

        Schema::create('cat_010', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 100)->notNullable();
        });

        Schema::create('cat_011', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 100)->notNullable();
        });

        Schema::create('cat_012', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 50)->notNullable();
            $table->primary('codigo');
        });

        Schema::create('cat_013', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 100)->notNullable();
            $table->string('departamento', 2)->notNullable();
            $table->foreign('departamento')->references('codigo')->on('cat_012');
        });

        Schema::create('cat_014', function (Blueprint $table) {
            $table->string('codigo', 2)->notNullable();
            $table->string('valores', 100)->notNullable();
        });

        Schema::create('cat_015', function (Blueprint $table) {
            $table->string('codigo', 3)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_016', function (Blueprint $table) {
            $table->string('codigo', 3)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_017', function (Blueprint $table) {
            $table->string('codigo', 3)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_018', function (Blueprint $table) {
            $table->string('codigo', 3)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_019', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->text('valores')->notNullable();
        });

        Schema::create('cat_020', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_021', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_022', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_023', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_024', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_025', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_026', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_027', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_028', function (Blueprint $table) {
            $table->string('codigo', 20)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_029', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_030', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_031', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });

        Schema::create('cat_032', function (Blueprint $table) {
            $table->string('codigo', 10)->notNullable();
            $table->string('valores', 255)->notNullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_032');
        Schema::dropIfExists('cat_031');
        Schema::dropIfExists('cat_030');
        Schema::dropIfExists('cat_029');
        Schema::dropIfExists('cat_028');
        Schema::dropIfExists('cat_027');
        Schema::dropIfExists('cat_026');
        Schema::dropIfExists('cat_025');
        Schema::dropIfExists('cat_024');
        Schema::dropIfExists('cat_023');
        Schema::dropIfExists('cat_022');
        Schema::dropIfExists('cat_021');
        Schema::dropIfExists('cat_020');
        Schema::dropIfExists('cat_019');
        Schema::dropIfExists('cat_018');
        Schema::dropIfExists('cat_017');
        Schema::dropIfExists('cat_016');
        Schema::dropIfExists('cat_015');
        Schema::dropIfExists('cat_014');
        Schema::dropIfExists('cat_013');
        Schema::dropIfExists('cat_012');
        Schema::dropIfExists('cat_011');
        Schema::dropIfExists('cat_010');
        Schema::dropIfExists('cat_009');
        Schema::dropIfExists('cat_007');
        Schema::dropIfExists('cat_006');
        Schema::dropIfExists('cat_005');
        Schema::dropIfExists('cat_004');
        Schema::dropIfExists('cat_003');
        Schema::dropIfExists('cat_002');
        Schema::dropIfExists('cat_001');
    }
};
