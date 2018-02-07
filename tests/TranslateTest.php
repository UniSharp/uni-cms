<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Page;
use UniSharp\UniCMS\Translation;
use Illuminate\Support\Facades\Lang;

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

    public function testDelete()
    {
        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->name = 'foo';

        $page->save();

        $this->assertCount(1, Translation::all());

        $page->delete();

        $this->assertCount(0, Translation::all());
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

    public function testToArray()
    {
        Lang::shouldReceive('getLocale')->andReturn('en');
        Lang::shouldReceive('getFallback')->andReturn('en');

        $page = new class(['slug' => 'foo']) extends Page {
            public $table = 'pages';
            protected $translatedAttributes = ['name', 'title'];
        };

        $page->translate('en')->name = 'foo';

        $page->save();

        $this->assertEquals(['name' => 'foo'], $page->fresh()->translationsToArray('en'));
        $this->assertEquals('foo', $page->fresh()->translate('en')->toArray()['name']);
        $this->assertNull($page->fresh()->translate('en')->toArray()['title']);
        $this->assertArrayNotHasKey('translations', $page->fresh()->translate('en')->toArray());

        $page->translate('en')->title = 'bar';

        $this->assertEquals(['name' => 'foo', 'title' => 'bar'], $page->translationsToArray('en'));
        $this->assertEquals('foo', $page->translate('en')->toArray()['name']);
        $this->assertEquals('bar', $page->translate('en')->toArray()['title']);
        $this->assertArrayNotHasKey('translations', $page->translate('en')->toArray());
    }

    public function testDefaultLanguage()
    {
        Lang::shouldReceive('getLocale')->twice()->andReturn('en');

        $page = new Page(['slug' => 'foo']);

        $page->name = 'foo';

        $this->assertEquals('foo', $page->name);
        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));

        $page->save();
    }

    public function testFallbackLanguage()
    {
        Lang::shouldReceive('getFallback')->times(10)->andReturn('en');

        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->name = 'foo';

        $this->assertEquals('foo', $page->translate('de')->name);
        $this->assertEquals('foo', $page->getTranslation('de', 'name'));

        $page->save();

        $this->assertEquals('foo', $page->translate('de')->name);
        $this->assertEquals('foo', $page->getTranslation('de', 'name'));
        $this->assertEquals('foo', $page->fresh()->translate('de')->name);
        $this->assertEquals('foo', $page->fresh()->getTranslation('de', 'name'));
    }

    public function testToArrayWithFallbackLang()
    {
        Lang::shouldReceive('getLocale')->andReturn('en');
        Lang::shouldReceive('getFallback')->andReturn('en');

        $page = new class(['slug' => 'foo']) extends Page {
            public $table = 'pages';
            protected $translatedAttributes = ['name', 'title'];
        };

        $page->translate('en')->name = 'foo';

        $page->save();

        $this->assertEquals(['name' => 'foo'], $page->fresh()->translationsToArray('de'));
        $this->assertEquals('foo', $page->fresh()->translate('de')->toArray()['name']);

        $page->translate('de')->name = 'FOO';
        $page->translate('en')->title = 'bar';

        $this->assertEquals(['name' => 'FOO', 'title' => 'bar'], $page->translationsToArray('de'));
        $this->assertEquals('FOO', $page->translate('de')->toArray()['name']);
        $this->assertEquals('bar', $page->translate('de')->toArray()['title']);
    }

    public function testFill()
    {
        Lang::shouldReceive('getLocale')->andReturn('en');
        Lang::shouldReceive('getFallback')->andReturn('en');

        $page = new Page(['slug' => 'foo', 'name' => 'foo']);

        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));

        $page->save();

        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));
        $this->assertEquals('foo', $page->fresh()->translate('en')->name);
        $this->assertEquals('foo', $page->fresh()->getTranslation('en', 'name'));
    }

    public function testCreate()
    {
        $page = Page::translate('en')->create(['slug' => 'foo', 'name' => 'foo']);

        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));
        $this->assertEquals('foo', $page->fresh()->translate('en')->name);
        $this->assertEquals('foo', $page->fresh()->getTranslation('en', 'name'));
    }

    public function testCreateWithDefaultLanguage()
    {
        Lang::shouldReceive('getLocale')->andReturn('en');
        Lang::shouldReceive('getFallback')->andReturn('en');

        $page = Page::create(['slug' => 'foo', 'name' => 'foo']);

        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));
        $this->assertEquals('foo', $page->fresh()->translate('en')->name);
        $this->assertEquals('foo', $page->fresh()->getTranslation('en', 'name'));
    }

    public function testFillwithUnguraded()
    {
        Lang::shouldReceive('getLocale')->andReturn('en');
        Lang::shouldReceive('getFallback')->andReturn('en');

        Page::unguarded(function () {
            $page = new Page(['slug' => 'foo', 'name' => 'foo']);

            $this->assertEquals('foo', $page->translate('de')->name);
            $this->assertEquals('foo', $page->getTranslation('de', 'name'));

            $page->save();

            $this->assertEquals('foo', $page->translate('de')->name);
            $this->assertEquals('foo', $page->getTranslation('de', 'name'));
            $this->assertEquals('foo', $page->fresh()->translate('de')->name);
            $this->assertEquals('foo', $page->fresh()->getTranslation('de', 'name'));
        });
    }

    public function testCast()
    {
        Lang::shouldReceive('getFallback')->andReturn('en');

        $page = new class(['slug' => 'foo']) extends Page {
            public $table = 'pages';
            protected $translatedAttributes = ['data'];
            protected $casts = ['data' => 'json'];
        };

        $page->translate('en')->data = $data = ['foo' => 'bar'];

        $page->save();

        $this->assertSame($data, $page->fresh()->translate('en')->data);
        $this->assertSame(compact('data'), $page->fresh()->translationsToArray('en'));
        $this->assertSame($data, $page->fresh()->translate('en')->toArray()['data']);
    }

    public function testSaveWithNullValue()
    {
        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->name = null;

        $page->save();

        $this->assertCount(0, Translation::all());

        $this->assertFalse($page->isTranslationDirty());
    }

    public function testFillWithNullValue()
    {
        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->fill(['name' => null]);

        $page->save();

        $this->assertCount(0, Translation::all());

        $this->assertFalse($page->isTranslationDirty());
    }

    public function testUpdateWithNullValue()
    {
        $page = new Page(['slug' => 'foo']);

        $page->translate('en')->name = 'foo';

        $page->save();

        $this->assertCount(1, Translation::all());

        $page->translate('en')->name = null;

        $page->save();

        $this->assertCount(0, Translation::all());

        $this->assertFalse($page->isTranslationDirty());
    }
}
