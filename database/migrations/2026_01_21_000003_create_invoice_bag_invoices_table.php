<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_bag_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_bag_id')->constrained('invoice_bags')->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('business')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('invoice_uuid')->unique();
            $table->unsignedInteger('correlative');
            $table->string('status')->default('pending');
            $table->boolean('omitted_receptor')->default(false);
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->unsignedBigInteger('dte_id')->nullable();
            $table->json('customer_data')->nullable();
            $table->json('products');
            $table->json('totals')->nullable();
            $table->json('dte_snapshot')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->timestamps();

            $table->index(['invoice_bag_id', 'status']);
            $table->index(['business_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_bag_invoices');
    }
};
