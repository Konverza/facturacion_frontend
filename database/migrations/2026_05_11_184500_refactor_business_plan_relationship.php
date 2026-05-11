<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('business_plan', 'business_id')) {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->unsignedBigInteger('business_id')->nullable()->after('id');
            });
        }

        $businesses = DB::table('business')
            ->select('id', 'nit', 'plan_id')
            ->orderBy('id')
            ->get();

        foreach ($businesses as $business) {
            $existingPlan = DB::table('business_plan')
                ->where('nit', $business->nit)
                ->orderByDesc('id')
                ->first();

            if ($existingPlan) {
                DB::table('business_plan')
                    ->where('id', $existingPlan->id)
                    ->update([
                        'business_id' => $business->id,
                        'plan_id' => $existingPlan->plan_id ?? $business->plan_id,
                    ]);

                continue;
            }

            DB::table('business_plan')->insert([
                'business_id' => $business->id,
                'plan_id' => $business->plan_id,
                'dtes' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $duplicates = DB::table('business_plan')
            ->select('business_id', DB::raw('MAX(id) as keep_id'))
            ->whereNotNull('business_id')
            ->groupBy('business_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('business_plan')
                ->where('business_id', $duplicate->business_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        DB::table('business_plan')->whereNull('business_id')->delete();

        try {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->unique('business_id');
            });
        } catch (\Throwable $e) {
            // Unique index may already exist.
        }

        try {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->foreign('business_id')
                    ->references('id')
                    ->on('business')
                    ->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // Foreign key may already exist.
        }

        if (Schema::hasColumn('business_plan', 'nit')) {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->dropColumn('nit');
            });
        }

        if (Schema::hasColumn('business', 'plan_id')) {
            try {
                Schema::table('business', function (Blueprint $table) {
                    $table->dropForeign('business_plan_id_foreign');
                });
            } catch (\Throwable $e) {
                // Foreign key may not exist on some environments.
            }

            Schema::table('business', function (Blueprint $table) {
                $table->dropColumn('plan_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('business', 'plan_id')) {
            Schema::table('business', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('nombre');
            });
        }

        if (!Schema::hasColumn('business_plan', 'nit')) {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->string('nit')->nullable()->after('id');
            });
        }

        $plans = DB::table('business_plan')
            ->select('business_id', 'plan_id')
            ->whereNotNull('business_id')
            ->get();

        foreach ($plans as $plan) {
            DB::table('business')
                ->where('id', $plan->business_id)
                ->update(['plan_id' => $plan->plan_id]);
        }

        DB::table('business_plan')
            ->join('business', 'business.id', '=', 'business_plan.business_id')
            ->update(['business_plan.nit' => DB::raw('business.nit')]);

        try {
            Schema::table('business', function (Blueprint $table) {
                $table->foreign('plan_id')
                    ->references('id')
                    ->on('plans')
                    ->onUpdate('no action')
                    ->onDelete('no action');
            });
        } catch (\Throwable $e) {
            // Foreign key may already exist.
        }

        try {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->dropForeign(['business_id']);
            });
        } catch (\Throwable $e) {
            // Foreign key may not exist.
        }

        try {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->dropUnique(['business_id']);
            });
        } catch (\Throwable $e) {
            // Unique index may not exist.
        }

        if (Schema::hasColumn('business_plan', 'business_id')) {
            Schema::table('business_plan', function (Blueprint $table) {
                $table->dropColumn('business_id');
            });
        }

    }
};
