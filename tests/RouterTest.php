<?php

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase
{
    public function testDispatchCallsCorrectRoute(): void
    {
        $router = new Router();
        $called = false;

        $router->get('/test', static function () use (&$called): void {
            $called = true;
            echo 'ok';
        });

        ob_start();
        $router->dispatch('/test', 'GET');
        $output = ob_get_clean();

        $this->assertTrue($called);
        $this->assertSame('ok', $output);
    }

    public function testDispatchReturns404ForUnknownRoute(): void
    {
        $router = new Router();

        ob_start();
        $router->dispatch('/unknown', 'GET');
        $output = ob_get_clean();

        $this->assertStringContainsString('404 - Page not found', $output);
    }
}
