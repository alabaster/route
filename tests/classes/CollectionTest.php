<?php

use Alabaster\Route\Collection;
use Alabaster\Route\Route;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $routes = [
            ['product.details', ['POST' => 'handler0', 'GET' => 'handler1']],
            ['product.category', ['POST' => 'handler0', 'GET' => 'handler1', 'PUT' => 'handler2']],
        ];

        $collection = new Collection;

        foreach ($routes as $key => $data) {
            $route = new Route($data[0]);
            foreach ($data[1] as $method => $handler) {
                $route->setHandlers($method, $handler, ['*/*']);
            }
            $collection->addRoute($route);
        }

        $route0 = $collection->getRouteByName('product.details');
        $this->assertNotNull($route0);

        $route1 = $collection->getRouteByName('product.category');
        $this->assertNotNull($route1);

        $route2 = $collection->getRouteByName('product.variant');
        $this->assertNull($route2);

        // Testing serilization

        $data = serialize($collection);

        $newCollection = unserialize($data);

        $route0 = $newCollection->getRouteByName('product.details');
        $this->assertNotNull($route0);

        $route1 = $newCollection->getRouteByName('product.category');
        $this->assertNotNull($route1);

        $route2 = $newCollection->getRouteByName('product.variant');
        $this->assertNull($route2);
    }
}
