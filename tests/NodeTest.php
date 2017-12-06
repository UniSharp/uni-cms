<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Node;
use UniSharp\UniCMS\Page;

class NodeTest extends TestCase
{
    public function testMorphTo()
    {
        $node = new Node;

        $page = Page::create(['slug' => 'foo']);

        $node->page()->associate($page);

        $node->save();

        $this->assertTrue($node->fresh()->page->is($page));
    }

    public function testDeleted()
    {
        $node = new Node;

        $page = Page::create(['slug' => 'foo']);

        $node->page()->associate($page);

        $node->save();

        $this->assertNotNull(Page::find($page->id));

        $node->delete();

        $this->assertNull(Page::find($page->id));
    }
}
