<?php

use ElfSundae\Multimail\SwiftMailerManager;
use ElfSundae\Multimail\TransportManager;
use Illuminate\Contracts\Foundation\Application;
use Mockery as m;

class SwiftMailerManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(
            SwiftMailerManager::class,
            new SwiftMailerManager(m::mock(Application::class))
        );
    }

    public function testGetDefaultDriver()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('getDefaultDriver')->andReturn('foo');
        $manager = (new SwiftMailerManager(m::mock(Application::class)))
            ->setTransportManager($transportManager);
        $this->assertSame('foo', $manager->getDefaultDriver());
    }

    public function testSetDefaultDriver()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('setDefaultDriver')->with('foo');
        $manager = (new SwiftMailerManager(m::mock(Application::class)))
            ->setTransportManager($transportManager);
        $manager->setDefaultDriver('foo');
    }
}
