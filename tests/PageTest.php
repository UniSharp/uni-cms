<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Node;
use UniSharp\UniCMS\Page;

class PageTest extends TestCase
{
    public function testNode()
    {
        $page = Page::create(['slug' => 'foo']);

        $this->assertTrue($page->fresh()->node->is(Node::find(1)));
    }

    public function testWidgets()
    {
        $page = Page::create(['slug' => 'foo']);

        $widget = $page->widgets()->create([
            'type' => 'text-center'
        ]);

        $this->assertTrue($page->fresh()->widgets()->first()->is($widget));
    }

    public function testChildren()
    {
        $parent = Page::create(['slug' => 'foo']);

        $child = $parent->children()->create(['slug' => 'bar'])->fresh();
        $parent = $parent->fresh();

        $this->assertTrue($parent->children->contains($child));
        $this->assertTrue($parent->children()->first()->is($child));
        $this->assertTrue($parent->children()->where('slug', 'bar')->first()->is($child));
    }

    public function testRoot()
    {
        $root = Page::create(['slug' => 'foo']);

        $child = $root->children()->create(['slug' => 'bar'])->fresh();
        $root = $root->fresh();

        $this->assertTrue($child->root->is($root));
    }

    public function testParent()
    {
        $child = Page::create(['slug' => 'foo']);
        $parent = Page::create(['slug' => 'bar']);

        $this->assertNull($child->parent);

        $child->parent()->associate($parent)->save();

        $child = $child->fresh();
        $parent = $parent->fresh();

        $this->assertTrue($child->parent->is($parent));
    }
}
