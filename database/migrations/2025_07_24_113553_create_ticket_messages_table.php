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
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->index('ticket_messages_ticket_id_foreign');
            $table->unsignedBigInteger('user_id')->index('ticket_messages_user_id_foreign');
            $table->foreign(['ticket_id'])->references(['id'])->on('tickets')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
