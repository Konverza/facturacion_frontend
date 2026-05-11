<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NavBusinessProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer("layouts.partials.business.navbar", function ($view) {
            $user = User::with('businesses.business')->find(auth()->user()->id);
            $businesses = Business::whereIn(
                'id',
                $user->businesses->pluck('business_id')->toArray()
            )->get();
            $test_enviroment = session('ambiente') == '2' ? true : false;
            $maintenance_notice = env("MAINTENANCE_NOTICE") == '1' ?? false;

            $maintenance_start_raw = env("MAINTENANCE_START");
            $maintenance_duration = env("MAINTENANCE_DURATION", 15);
            $maintenance_start_date = $maintenance_start_raw;
            $maintenance_start_time = null;

            if (!empty($maintenance_start_raw)) {
                try {
                    $maintenance_start = Carbon::parse($maintenance_start_raw, config('app.timezone'));
                    $maintenance_start_date = $maintenance_start->format('d/m/Y');
                    $maintenance_start_time = $maintenance_start->format('h:i A');
                } catch (\Throwable $e) {
                    $maintenance_start_date = $maintenance_start_raw;
                    $maintenance_start_time = null;
                }
            }

            $view->with([
                "businesses" => $businesses,
                "test_enviroment" => $test_enviroment,
                "maintenance_notice" => $maintenance_notice,
                "maintenance_start_date" => $maintenance_start_date,
                "maintenance_start_time" => $maintenance_start_time,
                "maintenance_duration" => $maintenance_duration,
            ]);
        });
    }
}
