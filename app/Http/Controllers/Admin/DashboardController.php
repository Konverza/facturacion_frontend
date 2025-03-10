<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{

    public $octopus_url;

    public function __construct()
    {
        $this->octopus_url = env("OCTOPUS_API_URL");
    }

    public function index()
    {
        $octopus_statistics = Http::get($this->octopus_url . '/dtes/statistics/')->json();
        $customers = DB::table('business')->get();
        $business = Business::with('plan')->orderBy('created_at', 'desc')->take(5)->get();
        return view(
            'admin.dashboard.index',
            [
                'octopus_statistics' => $octopus_statistics,
                'customers' => $customers,
                'business' => $business
            ]
        );
    }
}
