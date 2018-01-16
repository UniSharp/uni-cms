<?php

namespace Tests;

use Mockery as m;
use CreateUniCMSTables;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $app = m::mock(Container::class);
        $app->shouldReceive('instance');
        $app->shouldReceive('offsetGet')->with('db')->andReturn(
            m::mock('db')->shouldReceive('connection')->andReturn(
                m::mock('connection')->shouldReceive('getSchemaBuilder')->andReturn('schema')->getMock()
            )->getMock()
        );
        $app->shouldReceive('offsetGet');

        Schema::setFacadeApplication($app);
        Schema::swap(Manager::schema());

        (new CreateUniCMSTables)->up();
    }

    public function tearDown()
    {
        (new CreateUniCMSTables)->down();

        m::close();

        Facade::clearResolvedInstances();

        parent::tearDown();
    }
}
