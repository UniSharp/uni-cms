<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Page;

class TranslateTest extends TestCase
{
    public function testSetAndGetTranslation()
    {
        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->name = 'foo';

        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));

        $page->save();

        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));
        $this->assertEquals('foo', $page->fresh()->translate('en')->name);
        $this->assertEquals('foo', $page->fresh()->getTranslation('en', 'name'));

        $page->translate('en')->name = 'FOO';
        $page->translate('de')->name = 'BAR';

        $this->assertEquals('FOO', $page->translate('en')->name);
        $this->assertEquals('FOO', $page->getTranslation('en', 'name'));
        $this->assertEquals('BAR', $page->translate('de')->name);
        $this->assertEquals('BAR', $page->getTranslation('de', 'name'));

        $page->save();

        $this->assertEquals('FOO', $page->translate('en')->name);
        $this->assertEquals('FOO', $page->getTranslation('en', 'name'));
        $this->assertEquals('BAR', $page->translate('de')->name);
        $this->assertEquals('BAR', $page->getTranslation('de', 'name'));
        $this->assertEquals('FOO', $page->fresh()->translate('en')->name);
        $this->assertEquals('FOO', $page->fresh()->getTranslation('en', 'name'));
        $this->assertEquals('BAR', $page->fresh()->translate('de')->name);
        $this->assertEquals('BAR', $page->fresh()->getTranslation('de', 'name'));
    }

    public function testDirty()
    {
        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->name = 'foo';

        $this->assertTrue($page->isTranslationDirty());
        $this->assertEquals(['en' => ['name' => 'foo']], $page->getTranslationDirty());

        $page->save();

        $this->assertFalse($page->isTranslationDirty());
        $this->assertEquals(['en' => []], $page->getTranslationDirty());

        $page->translate('en')->name = 'FOO';
        $page->translate('de')->name = 'BAR';

        $this->assertTrue($page->isTranslationDirty());
        $this->assertEquals(['en' => ['name' => 'FOO'], 'de' => ['name' => 'BAR']], $page->getTranslationDirty());

        $page->save();

        $this->assertFalse($page->isTranslationDirty());
        $this->assertEquals(['en' => [], 'de' => []], $page->getTranslationDirty());
    }
}