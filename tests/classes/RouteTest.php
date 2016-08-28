<?php

use Alabaster\Route\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{

    public function routeProvider()
    {
        return [
            ['store.index', ['POST' => 'handler0', 'GET' => 'handler1'], ['PUT', 'DELETE']],
            ['store.product', ['POST' => 'handler0', 'GET' => 'handler1', 'PUT' => 'handler2'], ['DELETE', 'PATCH']],
        ];
    }

    /**
     * @dataProvider routeProvider
     */
    public function testGetRouteHandlers($pattern, $handlers, $notSupported)
    {
        $route = new Route($pattern);

        foreach ($handlers as $method => $handler) {
            $route->setHandlers($method, $handler, ['*/*']);
        }

        foreach ($handlers as $method => $handler) {
            $handles = $route->getHandlersForMethod($method);
            $this->assertArrayHasKey('*/*', $handles);
        }

        foreach ($notSupported as $method) {
            $handles = $route->getHandlersForMethod($method);
            $this->assertNull($handles);
        }

    }

    /**
     * @dataProvider routeProvider
     * @expectedException UnexpectedValueException
     */
    public function testContentTypeException($pattern, $handlers, $notSupported)
    {
        $route = new Route($pattern);

        foreach ($handlers as $method => $handler) {
            $route->setHandlers($method, $handler, []);
        }
    }
}
