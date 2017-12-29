<?php

namespace Tests;

use Mockery as m;
use UniSharp\UniCMS\Page;
use UniSharp\UniCMS\Translation;

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
        $page = new class(['slug' => 'foo']) extends Page {
            public $table = 'pages';
            protected $translatedAttributes = ['name', 'title'];
        };

        $page->translate('en')->name = 'foo';

        $page->save();

        $this->assertEquals(['name' => 'foo'], $page->fresh()->translationsToArray('en'));
        $this->assertEquals('foo', $page->fresh()->translate('en')->toArray()['name']);
        $this->assertArrayNotHasKey('translations', $page->fresh()->translate('en')->toArray());

        $page->translate('en')->title = 'bar';

        $this->assertEquals(['name' => 'foo', 'title' => 'bar'], $page->translationsToArray('en'));
        $this->assertEquals('foo', $page->translate('en')->toArray()['name']);
        $this->assertEquals('bar', $page->translate('en')->toArray()['title']);
        $this->assertArrayNotHasKey('translations', $page->translate('en')->toArray());
    }

    public function testDefaultLanguage()
    {
        $page = new Page(['slug' => 'foo']);

        $page->name = 'foo';

        $this->assertEquals('foo', $page->name);
        $this->assertEquals('foo', $page->translate('en')->name);
        $this->assertEquals('foo', $page->getTranslation('en', 'name'));

        $page->save();
    }

    public function testFallbackLanguage()
    {
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
        $page = new Page(['slug' => 'foo', 'name' => 'foo']);

        $this->assertEquals('foo', $page->translate('de')->name);
        $this->assertEquals('foo', $page->getTranslation('de', 'name'));

        $page->save();

        $this->assertEquals('foo', $page->translate('de')->name);
        $this->assertEquals('foo', $page->getTranslation('de', 'name'));
        $this->assertEquals('foo', $page->fresh()->translate('de')->name);
        $this->assertEquals('foo', $page->fresh()->getTranslation('de', 'name'));
    }

    public function testFillwithUnguraded()
    {
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
}
