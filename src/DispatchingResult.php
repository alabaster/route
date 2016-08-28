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
class DispatchingResult
{

    const ROUTE_FOUND = 0;

    const ROUTE_NOT_FOUND = 1;

    const HTTP_METHOD_NOT_SUPPORTED = 2;

    const HTTP_RESPONSE_TYPE_NOT_SUPPORTED = 3;

    protected $code;

    protected $handler;

    public function __construct($code, $handler)
    {
        $this->code = $code;
        $this->handler = $handler;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getHandler()
    {
        return $this->handler;
    }
}
