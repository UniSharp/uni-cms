<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Node;
use UniSharp\UniCMS\Page;

class PageTest extends TestCase
{
    public function testNode()
    {
        $node = new Node;

        $page = Page::create(['slug' => 'foo']);

        $node->page()->associate($page);

        $node->save();

        $this->assertTrue($page->fresh()->node->is($node));
    }

    public function testWidgets()
    {
        $page = Page::create(['slug' => 'foo']);

        $widget = $page->widgets()->create([
            'type' => 'text-center'
        ]);

        $this->assertTrue($page->fresh()->widgets()->first()->is($widget));
    }
}
