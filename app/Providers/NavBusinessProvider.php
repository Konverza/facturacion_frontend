<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\User;
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
            $view->with([
                "businesses" => $businesses,
                "test_enviroment" => $test_enviroment,
            ]);
        });
    }
}
