<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handler;
use Exceptions\Http\Client\NotFoundException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Request as LumenRequest;
use Mockery;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

final class HandlerTest extends TestCase
{
    public function testRenderingValidationException()
    {
        $handler = new Handler();

        $symfonyRequest = SymfonyRequest::create('/', 'GET');
        $request = LumenRequest::createFromBase($symfonyRequest);


        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive('errors->toArray')
            ->andReturn(['some' => 'error']);
        $exception = new ValidationException($validator);

        $response = $handler->render($request, $exception);

        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertArrayHasKey('validation_details', json_decode($content, true));
    }

    public function testRenderingStdException()
    {
        $handler = new Handler();

        $symfonyRequest = SymfonyRequest::create('/', 'GET');
        $request = LumenRequest::createFromBase($symfonyRequest);
        ;
        $exception = new NotFoundException();

        $response = $handler->render($request, $exception);

        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertArrayNotHasKey('validation_details', json_decode($content, true));
    }
}
