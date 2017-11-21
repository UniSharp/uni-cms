<?php

namespace Tests;

use App\Main;
use Mockery as m;

class UnitTest extends TestCase
{
    public function testFoo()
    {
        $main = new Main;

        $this->assertEquals('bar', $main->foo());
    }
}
