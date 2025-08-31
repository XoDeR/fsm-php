<?php

use App\Calc;
use PHPUnit\Framework\TestCase;

class CalcTest extends TestCase
{
    public function testAdd()
    {
        $calc = new Calc();
        $this->assertEquals(4, $calc->add(2, 2));
    }
}
