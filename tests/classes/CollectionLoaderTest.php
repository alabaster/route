<?php

use Alabaster\Route\CollectionLoader;

class CollectionLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $loader = new CollectionLoader;
        $collection = $loader->getFromYamlFile(TEST_DIR . 'files/routes.yaml');

        $route = $collection->getRouteByName('product.details');
        $this->assertNotNull($route);

        $route = $collection->getRouteByName('category.listing');
        $this->assertNotNull($route);

        $route = $collection->getRouteByName('product.variant');
        $this->assertNull($route);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNotFoundCollection()
    {
        $loader = new CollectionLoader;
        $collection = $loader->getFromYamlFile(TEST_DIR . 'files/non_existing.yaml');
    }
}
