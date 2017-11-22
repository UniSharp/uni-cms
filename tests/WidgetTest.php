<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Node;
use UniSharp\UniCMS\Page;

class WidgetTest extends TestCase
{
    public function testPage()
    {
        $page = Page::create(['slug' => 'foo']);

        $widget = $page->widgets()->create();

        $this->assertTrue($widget->fresh()->page->is($page));
    }

    public function testAutoInsertSort()
    {
        $page = Page::create(['slug' => 'foo']);

        $this->assertEquals(0, $page->widgets()->create()->sort);
        $this->assertEquals(1, $page->widgets()->create()->sort);
        $this->assertEquals(2, $page->widgets()->create()->sort);
    }

    public function testAutoSort()
    {
        $page = Page::create(['slug' => 'foo']);

        $widgets = [
            'widgetA' => 2,
            'widgetB' => 1,
            'widgetC' => 0,
        ];

        foreach ($widgets as $var => $sort) {
            $$var = $page->widgets()->create();

            $$var->sort = $sort;

            $$var->save();
        }

        $this->assertEquals(range(0, 2), $page->fresh()->widgets->pluck('sort')->toArray());
    }
}
