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
 * This class get a colletion of accepted content types
 * (typically from an HTTP request header).
 * And submit them to a list of supported types.
 * This class will list of the matching types ordered from best to worst quality.
 *
 * @author liva Ramarolahy <liva.ramarolahy@gmail.com>
 */
class TypeResolver
{
    /**
     * Indicates an exact matching type
     */
    const EXACT_MATCH = 0;

    /**
     * indicates a wildcard matching type (especially for subtypes)
     */
    const WILDCARD_MATCH = 1;

    /**
     * Store list of accepted content types
     */
    protected $acceptedTypes;

    /**
     *
     */
    protected $contentSearchTree;

    /**
     * Store the list of all matching type
     */
    protected $bestSupportedTypes;

    protected $foundMatchingTypes;

    public function __construct()
    {
        $this->foundMatchingTypes = false;
    }

    public function setAcceptedTypes(array $types)
    {
        $this->acceptedTypes = $types;
    }

    public function matchTypes($supportedTypes)
    {
        $this->buildContentSearchTree($supportedTypes);

        $this->bestSupportedTypes = [];

        foreach ($this->acceptedTypes as $accepted => $quality) {
            list($type, $subtype) = explode('/', strtolower($accepted));
            if (isset($this->contentSearchTree[$type])) {
                $this->matchBestSubType($type, $subtype, $quality, $this->contentSearchTree[$type]);
            }
        }

        $this->matchWildSubtypes($supportedTypes);

        krsort($this->bestSupportedTypes, SORT_NUMERIC);

        return $this->bestSupportedTypes;
    }

    protected function matchWildSubtypes($supportedTypes)
    {
        if ($this->foundMatchingTypes === false && isset($this->acceptedTypes['*/*'])) {
            foreach ($supportedTypes as $type) {
                $this->addSupportedType($this->acceptedTypes['*/*'], self::WILDCARD_MATCH, $type);
            }
        }

        if ($this->foundMatchingTypes === false && in_array('*/*', $supportedTypes)) {
            $this->addSupportedType('0.0', self::WILDCARD_MATCH, '*/*');
        }
    }

    protected function matchBestSubType($type, $subtype, $quality, $subtypes)
    {
        if (in_array($subtype, $subtypes)) {
            $this->addSupportedType($quality, self::EXACT_MATCH, $type.'/'.$subtype);
        }
        if (in_array('*', $subtypes)) {
            $this->addSupportedType($quality, self::WILDCARD_MATCH, $type.'/*');
        }
        $this->foundMatchingTypes = true;
    }

    protected function addSupportedType($quality, $matching, $type)
    {
        $this->bestSupportedTypes[$quality][$matching][] = $type;
        $this->foundMatchingTypes = true;
    }

    protected function buildContentSearchTree($supportedTypes)
    {
        foreach ($supportedTypes as $key => $type) {
            list($main, $sub) = explode('/', $type);
            $this->contentSearchTree[$main][] = $sub;
        }
    }
}
