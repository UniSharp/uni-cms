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

        $widget = $page->widgets()->create(['type' => 'text-center']);

        $this->assertTrue($widget->fresh()->page->is($page));
    }

    public function testAutoSortOnCreatingAndDeleted()
    {
        $page = Page::create(['slug' => 'foo']);

        $this->assertEquals(0, ($widgetA = $page->widgets()->create(['type' => 'text-center']))->sort);
        $this->assertEquals(1, ($widgetB = $page->widgets()->create(['type' => 'text-center']))->sort);
        $this->assertEquals(2, ($widgetC = $page->widgets()->create(['type' => 'text-center']))->sort);

        $widgetA->delete();

        $this->assertEquals(0, $widgetB->fresh()->sort);
        $this->assertEquals(1, $widgetC->fresh()->sort);
        $this->assertEquals(2, $page->widgets()->create(['type' => 'text-center'])->sort);
    }

    public function testAutoSortOnListing()
    {
        $page = Page::create(['slug' => 'foo']);

        $widgets = [
            'widgetA' => 2,
            'widgetB' => 1,
            'widgetC' => 0,
        ];

        foreach ($widgets as $var => $sort) {
            $$var = $page->widgets()->create(['type' => 'text-center']);

            $$var->sort = $sort;

            $$var->save();
        }

        $this->assertEquals(range(0, 2), $page->fresh()->widgets->pluck('sort')->toArray());
    }
}
