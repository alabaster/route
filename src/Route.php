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

/**
 * Handle HTTP request
 */

class Route
{
    protected $name;

    protected $routeTables;

    /**
     * Initialize a route item
     */
    public function __construct($name)
    {
        $this->routeTables = [];
        $this->name = $name;
    }

    /**
     * @throws UnexpectedValueException
     */
    public function setHandlers($method, $handler, array $supportedTypes)
    {
        if (count($supportedTypes) == 0) {
            throw new \UnexpectedValueException("Supported response type cannot be empty");
        }

        $method = strtoupper($method);
        if (!isset($this->routeTables[$method])) {
            $this->routeTables[$method] = [];
        }

        foreach ($supportedTypes as $type) {
            $this->routeTables[$method][$type] = $handler;
        }
    }

    /**
     *
     */
    public function getHandlersForMethod($method)
    {
        $method = strtoupper($method);
        return array_key_exists($method, $this->routeTables) ? $this->routeTables[$method] : null;
    }

    /**
     * Get the name of the current route
     */
    public function getName()
    {
        return $this->name;
    }
}
