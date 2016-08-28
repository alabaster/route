<?php

/*
 * The MIT License
 *
 * Copyright (c) 2016 liva Ramarolahy http://liva.ramarolahy.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Alabaster\Route;

use Alabaster\Route\Route as RouteRoute;
use Symfony\Component\Yaml\Parser;

/**
 * Description of Cache.
 *
 * @author liva Ramarolahy <liva.ramarolahy@gmail.com>
 */
class CollectionLoader
{

    protected $collection;

    /**
     * Load a route collection from a YAML configuration file.
     *
     * @throws Symfony\Component\Yaml\Exception\ParseException
     * @throws RuntimeException;
     */
    public function getFromYamlFile($file)
    {
        if (!is_readable($file)) {
            throw new \RuntimeException("Inaccessible route configuration file : ".$file);
        }

        $yaml = new Parser();
        $routes = $yaml->parse(file_get_contents($file));

        $this->collection = new Collection();

        if (is_array($routes)) {
            foreach ($routes as $name => $methods) {
                $this->addRouteFromArray($name, $methods);
            }
        }

        return $this->collection;
    }

    protected function addRouteFromArray($name, $methods)
    {
        $route = new RouteRoute($name);
        foreach ($methods as $method => $handlers) {
            foreach ($handlers as $handler) {
                $types = isset($handler['types']) ? $handler['types'] : ['*/*'];
                $route->setHandlers($method, $handler['handler'], $types);
            }
        }
        $this->collection->addRoute($route);
    }
}
