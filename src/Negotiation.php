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
 * This class is used to manipulate a path segment in a route tree traversal.
 * It can be considered as a tree leaf.
 *
 * @author liva Ramarolahy <liva.ramarolahy@gmail.com>
 */
class Negotiation
{
    protected $acceptedTypes;

    protected $ordered;

    public function __construct()
    {
        $this->acceptedTypes = [];
    }

    public function addUserAgentContentType($type, $quality)
    {
        $this->acceptedTypes[strtolower($type)] = floor($quality * 10);
        $this->ordered = false;
    }

    public function getBestSupportedType($supportedTypes)
    {
        $this->orderAcceptedTypes();
        $resolver = new TypeResolver();
        $resolver->setAcceptedTypes($this->acceptedTypes);
        $bestSupportedTypes = $resolver->matchTypes($supportedTypes);

        return count($bestSupportedTypes)
                ? $this->getBestQualityContentType($bestSupportedTypes)
                : false;
    }

    protected function getBestQualityContentType($bestSupportedTypes)
    {
        $bestSupportedType = false;
        $matches = array_values($bestSupportedTypes)[0];
        if (isset($matches[TypeResolver::EXACT_MATCH])) {
            $bestSupportedType = $matches[TypeResolver::EXACT_MATCH][0];
        }
        if (isset($matches[TypeResolver::WILDCARD_MATCH])) {
            $bestSupportedType = $matches[TypeResolver::WILDCARD_MATCH][0];
        }
        return $bestSupportedType;
    }

    /**
     * Order the content types based on quality
     */
    protected function orderAcceptedTypes()
    {
        if (!$this->ordered) {
            asort($this->acceptedTypes, SORT_NUMERIC);
            $this->ordered = true;
        }
    }
}
