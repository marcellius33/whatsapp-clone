<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class RequestHelper
{
    public static function limit(Request $request, int $default = 15): int
    {
        return min((int) $request->input('limit', $default), 10000);
    }

    public static function createServerRequest(Request $request): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $factory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        return $factory->createRequest($request);
    }
}
