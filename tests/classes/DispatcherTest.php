<?php

use Alabaster\Route\Route;
use Alabaster\Route\Collection;
use Alabaster\Route\Dispatcher;
use Alabaster\Route\DispatchingResult;

class DispatcherTest extends PHPUnit_Framework_TestCase
{

    public function routeProvider()
    {
        return [
            ['photo', [['POST', 'handler0', ['application/*']], ['GET', 'handler1', ['application/*']]]],
            ['image', [['POST', 'handler0', ['application/*']], ['GET', 'handler1', ['application/*']]]]
        ];
    }

    /**
     * @dataProvider routeProvider
     */
    public function testGetRouteHandlers($name, $handlers)
    {
        $collection = new Collection;

        $route = new Route($name);

        foreach ($handlers as $handler) {
            list($method, $function, $contentTypes) = $handler;
            $route->setHandlers($method, $function, $contentTypes);
        }

        $collection->addRoute($route);

        $dispatcher = new Dispatcher();
        $dispatcher->useCollection($collection);

        // Route found

        $result = $dispatcher->dispatch('GET', $name, ['application/json' => 0.9]);
        $this->assertEquals(DispatchingResult::ROUTE_FOUND, $result->getCode());
        $this->assertEquals('handler1', $result->getHandler());

        // Route not found

        $result = $dispatcher->dispatch('GET', 'photos', ['application/json' => 0.9]);
        $this->assertEquals(DispatchingResult::ROUTE_NOT_FOUND, $result->getCode());

        // Route not found

        $result = $dispatcher->dispatch('PUT', $name, ['application/json' => 0.9]);
        $this->assertEquals(DispatchingResult::HTTP_METHOD_NOT_SUPPORTED, $result->getCode());

        // Route content type not supported

        $result = $dispatcher->dispatch('GET', $name, ['text/html' => 0.9]);
        $this->assertEquals(DispatchingResult::HTTP_RESPONSE_TYPE_NOT_SUPPORTED, $result->getCode());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCollectionException()
    {
        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch('GET', 'archive/01', []);
    }


    /**
     * @expectedException UnexpectedValueException
     */
    public function testContentTypeException()
    {
        $collection = new Collection;
        $dispatcher = new Dispatcher();
        $dispatcher->useCollection($collection);
        $result = $dispatcher->dispatch('GET', 'archive/01', []);
    }

    public function routeProvider3()
    {
        return [
            ['archive', [['GET', 'handler0', ['application/*']]]]
        ];
    }

    /**
     * @dataProvider routeProvider3
     */
    public function testHeadMethodMatching($pattern, $handlers)
    {
        $collection = new Collection;

        $route = new Route($pattern);

        foreach ($handlers as $handler) {
            list($method, $function, $contentTypes) = $handler;
            $route->setHandlers($method, $function, $contentTypes);
        }

        $collection->addRoute($route);

        $dispatcher = new Dispatcher();
        $dispatcher->useCollection($collection);

        // Route found

        $result = $dispatcher->dispatch('GET', 'archive', ['application/json' => 0.9]);
        $this->assertEquals(DispatchingResult::ROUTE_FOUND, $result->getCode());

        $result = $dispatcher->dispatch('HEAD', 'archive', ['application/json' => 0.9]);
        $this->assertEquals(DispatchingResult::ROUTE_FOUND, $result->getCode());

        // Now disable matching

        $dispatcher->matchMissingHeadHandlerWithGetHandler(false);
        $result = $dispatcher->dispatch('HEAD', 'archive', ['application/json' => 0.9]);
        $this->assertEquals(DispatchingResult::HTTP_METHOD_NOT_SUPPORTED, $result->getCode());
    }
}
