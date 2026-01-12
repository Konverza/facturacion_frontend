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
        Schema::create('dte_import_processes', function (Blueprint $table) {
            $table->id();
            $table->string('nit');
            $table->enum('status', ['pending', 'downloading', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('filename')->nullable();
            $table->integer('total_dtes')->default(0);
            $table->integer('processed_dtes')->default(0);
            $table->integer('failed_dtes')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['nit', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dte_import_processes');
    }
};
