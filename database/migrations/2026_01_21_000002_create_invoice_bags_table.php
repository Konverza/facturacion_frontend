<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_bags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('business')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('bag_date');
            $table->unsignedInteger('correlative');
            $table->string('bag_code')->unique();
            $table->string('status')->default('open');
            $table->string('sent_dte_codigo')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'bag_date', 'correlative']);
            $table->index(['business_id', 'user_id', 'bag_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_bags');
    }
};
