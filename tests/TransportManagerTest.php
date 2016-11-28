<?php

use ElfSundae\Multimail\TransportManager;
use Mockery as m;

class TransportManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(TransportManager::class, new TransportManager(m::mock('Illuminate\Contracts\Foundation\Application')));
    }

    public function testExtendDriver()
    {
        $manager = new TransportManager(m::mock('Illuminate\Contracts\Foundation\Application'));
        $manager->extend('foo', function () {
            return 'bar';
        });
        $this->assertEquals('bar', $manager->driver('foo'));
    }

    public function testGetAllDrivers()
    {
        $manager = new TransportManager(m::mock('Illuminate\Contracts\Foundation\Application'));
        $drivers = [
            'foo' => 'fooDriver',
            'bar' => 'barDriver',
        ];
        foreach ($drivers as $key => $value) {
            $manager->extend($key, function () use ($value) {
                return $value;
            });
            $manager->driver($key);
        }
        $this->assertEquals($drivers, $manager->getDrivers());
    }

    public function testResetDriverByName()
    {
        $manager = new TransportManager(m::mock('Illuminate\Contracts\Foundation\Application'));
        $drivers = [
            'foo' => 'fooDriver',
            'bar' => 'barDriver',
        ];
        foreach ($drivers as $key => $value) {
            $manager->extend($key, function () use ($value) {
                return $value;
            });
            $manager->driver($key);
        }

        $manager->resetDriver('foo');
        $this->assertEquals(['bar' => 'barDriver'], $manager->getDrivers());
    }

    public function testResetAllDrivers()
    {
        $manager = new TransportManager(m::mock('Illuminate\Contracts\Foundation\Application'));
        $drivers = [
            'foo' => 'fooDriver',
            'bar' => 'barDriver',
        ];
        foreach ($drivers as $key => $value) {
            $manager->extend($key, function () use ($value) {
                return $value;
            });
            $manager->driver($key);
        }

        $manager->resetDrivers();
        $this->assertEquals([], $manager->getDrivers());
    }
}
