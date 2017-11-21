<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Node;

class UnitTest extends TestCase
{
    public function testNode()
    {
        $node = new Node;

        $this->assertTrue($node->is($node));
    }
}
