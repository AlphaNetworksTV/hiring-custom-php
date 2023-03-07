<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Exceptions\Http\Client\BadRequestException;
use Exceptions\Http\HttpException as StdHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use KamranAhmed\Faulty\Handler as FaultyHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Inspired from kamranahmedse/laravel-faulty
 */
final class Handler extends FaultyHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * @inheritDoc
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ValidationException) {
            $ve = $e;

            /** @var BadRequestException $e */
            $e = BadRequestException::from($e);

            $data = $this->generateData($e);
            $data['validation_details'] = $ve->validator->errors()->toArray();
        } elseif ($e instanceof StdHttpException) {
            $data = $this->generateData($e);
        } else {
            return parent::handle($request, $e);
        }

        if (env('APP_DEBUG_TRACE', true) && method_exists($e, 'getTraceAsString')) {
            $data['trace'] = $e->getTraceAsString();
        }

        return response()->json($data, $data['status'], ['Content-Type' => 'application/problem+json']);
    }

    protected function generateData(StdHttpException $e)
    {
        return [
            'status' => $e->getCode(),
            'title' => class_basename($e),
            'detail' => $e->getMessage(),
            'type' => 'https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
        ];
    }
}
