<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller;

final class HomeController extends Controller
{
    public function home(): JsonResponse
    {
        return JsonResponse::create([
            'name' => config('app.name'),
            'version' => config('aepg.version'),
            'test' => 'hello world'
        ]);
    }
}
