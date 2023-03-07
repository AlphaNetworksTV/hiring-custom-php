<?php

declare(strict_types=1);

namespace Tests;

use App\Exceptions\Handler;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Laravel\Lumen\Application;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function debugWithExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class implements ExceptionHandler
        {
            private $handler;

            public function __construct()
            {
                $this->handler = new Handler();
            }

            /**
             * @inheritDoc
             */
            public function report(Exception $exception)
            {
                echo $exception->getMessage();
                echo $exception->getTraceAsString();
            }

            /**
             * @inheritDoc
             */
            public function shouldReport(Exception $e)
            {
                return true;
            }

            /**
             * @inheritDoc
             */
            public function render($request, Exception $e)
            {
                return $this->handler->render($request, $e);
            }

            /**
             * @inheritDoc
             */
            public function renderForConsole($output, Exception $e)
            {
                $this->handler->renderForConsole($output, $e);
            }
        });
    }
}
