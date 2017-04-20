<?php

namespace Tests;

use App\Main;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    public function testFoo()
    {
        $main = new Main;

        $this->assertEquals('bar', $main->foo());
    }
}
