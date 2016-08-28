<?php

use Alabaster\Route\DispatchingResult;

class DispatchingResultTest extends PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $result = new DispatchingResult(DispatchingResult::ROUTE_FOUND, ['handler0']);
        $code = $result->getCode();
        $handlers = $result->getHandler();

        $this->assertEquals(DispatchingResult::ROUTE_FOUND, $code);
        $this->assertContains('handler0', $handlers);
    }
}
