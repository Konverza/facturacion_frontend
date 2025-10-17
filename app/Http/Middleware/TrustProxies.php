<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies = '*'; // <– confía en el proxy que reenvía la petición

    protected $headers = Request::HEADER_X_FORWARDED_AWS_ELB
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT;
}
