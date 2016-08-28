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
 * Description of Cache.
 *
 * @author liva Ramarolahy <liva.ramarolahy@gmail.com>
 */
class Dispatcher
{
    protected $collection;

    protected $contentNegotation;

    protected $methodIsSupported;

    protected $matchHeadWithGet;

    public function __construct()
    {
        $this->matchHeadWithGet = true;
    }

    /**
     * Set the Collection to be used by the dispatcher
     */
    public function useCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * As following RFC 7231, all general-purpose servers MUST support
     * the methods GET and HEAD. This method help the programer to define
     * one single route for GET and HEAD. But it can be disabled if the app is
     * not a *general-purpose* one.
     */
    public function matchMissingHeadHandlerWithGetHandler($match)
    {
        $this->matchHeadWithGet = $match;
    }

    /**
     * Match the user URI to the current collection
     * @return Changi\Http\Route\DispatcherResult
     */
    public function dispatch($method, $name, array $acceptedContentTypes)
    {
        if (!$this->collection) {
            throw new \RuntimeException("Route collection is not set");
        }

        $this->prepareContentNegotiator($acceptedContentTypes);

        $route = $this->collection->getRouteByName($name);

        $dispatchingResult = null;

        if ($route) {

            $handlers = $this->getHandlersForMethod(strtoupper($method), $route);

            if ($this->methodIsSupported === false && $method == 'HEAD' && $this->matchHeadWithGet === true) {
                $handlers = $this->getHandlersForMethod('GET', $route);
            }

            $dispatchingResult = $this->getDispatchingResult($handlers);
        }
        else {
            $dispatchingResult = new DispatchingResult(DispatchingResult::ROUTE_NOT_FOUND, null, []);
        }

        return $dispatchingResult;
    }

    protected function getDispatchingResult($handlers)
    {
        $handler = null;
        $responseCode = DispatchingResult::ROUTE_FOUND;

        if (count($handlers) > 0) {
            $handler = array_values($handlers)[0]; ;
        }
        else {
            $responseCode = $this->methodIsSupported === true
                            ? DispatchingResult::HTTP_RESPONSE_TYPE_NOT_SUPPORTED
                            : DispatchingResult::HTTP_METHOD_NOT_SUPPORTED;
        }

        return new DispatchingResult($responseCode, $handler);
    }

    /**
     * Prepare Content Type negotiator
     * @throws \UnexpectedValueException
     */
    protected function prepareContentNegotiator(array $acceptedContentTypes)
    {
        $this->contentNegotation = new Negotiation();

        if (count($acceptedContentTypes) == 0) {
            throw new \UnexpectedValueException("Content types cannot be empty");
        }

        foreach ($acceptedContentTypes as $content => $quality) {
            $this->contentNegotation->addUserAgentContentType($content, $quality);
        }
    }

    /**
     * Get the list of handlers that can handle the current HTTP Method
     */
    protected function getHandlersForMethod($method, $route)
    {
        $handlers = $route->getHandlersForMethod($method);

        $this->methodIsSupported = (count($handlers) > 0);

        $filteredHandlers = [];

        if (is_array($handlers  )) {
            $filteredHandlers = $this->filterHandlersWithSupportedTypes($handlers);
        }

        return $filteredHandlers;
    }

    /**
     * Get the list of handlers that can handle the current supported content
     * types.
     */
    public function filterHandlersWithSupportedTypes($handlers)
    {
        $filteredHandlers = [];

        if ($handlers !== null) {
            $supportedTypes = array_keys($handlers);
            $bestSupportedType = $this->contentNegotation->getBestSupportedType($supportedTypes);

            if ($bestSupportedType !== false) {
                $filteredHandlers[] = $handlers[$bestSupportedType];
            }
        }

        return $filteredHandlers;
    }
}
