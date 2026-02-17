<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('has_api_access')->default(false)->after('invoice_bag_enabled');
            $table->string('api_key_hash', 64)->nullable()->unique()->after('has_api_access');
            $table->string('api_key_last4', 4)->nullable()->after('api_key_hash');
            $table->timestamp('api_key_created_at')->nullable()->after('api_key_last4');
        });
    }

    public function down(): void
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropUnique(['api_key_hash']);
            $table->dropColumn(['api_key_created_at', 'api_key_last4', 'api_key_hash', 'has_api_access']);
        });
    }
};
