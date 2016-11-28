<?php

use ElfSundae\Multimail\SwiftMailerManager;
use ElfSundae\Multimail\TransportManager;
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
            new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application'))
        );
    }

    public function testGetDefaultDriver()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('getDefaultDriver')->andReturn('foo');
        $manager = (new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application')))->setTransportManager($transportManager);
        $this->assertSame('foo', $manager->getDefaultDriver());
    }

    public function testSetDefaultDriver()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('setDefaultDriver')->with('foo');
        $manager = (new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application')))->setTransportManager($transportManager);
        $manager->setDefaultDriver('foo');
    }

    public function testGetMailer()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('driver')->with('foo')->andReturn(m::mock('Swift_Transport'));
        $manager = (new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application')))->setTransportManager($transportManager);
        $this->assertInstanceOf(Swift_Mailer::class, $manager->mailer('foo'));
    }

    public function testDriverHandlerWithClosure()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('driver')->with('foo')->andReturn(m::mock('Swift_Transport'));
        $manager = (new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application')))->setTransportManager($transportManager);
        $message = new Swift_Message;
        $manager->registerDriverHandler(function ($arg) use ($message) {
            $this->assertSame($message, $arg);

            return 'foo';
        });
        $this->assertInstanceOf(Swift_Mailer::class, $manager->mailerForMessage($message));
    }

    public function testResetMailer()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('resetDriver')->with('foo');
        $manager = (new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application')))->setTransportManager($transportManager);
        $manager->resetMailer('foo');
    }

    public function testResetMailers()
    {
        $transportManager = m::mock(TransportManager::class);
        $transportManager->shouldReceive('resetDrivers');
        $manager = (new SwiftMailerManager(m::mock('Illuminate\Contracts\Foundation\Application')))->setTransportManager($transportManager);
        $manager->resetMailers();
    }
}
