<?php

use ElfSundae\Multimail\Mailer;
use Mockery as m;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Mailer::class, $this->getMailer());
    }

    public function testMailersSwiftUsesDefaultMailerOfSwiftManager()
    {
        $mailer = $this->getMailer();
        $mailer->getSwiftMailerManager()->shouldReceive('mailer')->once()->andReturn('foo');
        $this->assertSame('foo', $mailer->getSwiftMailer());
    }

    public function testSetMailersSwiftSetsDefaultMailerOfSwiftManager()
    {
        $mailer = $this->getMailer();
        $swift = m::mock('Swift_Mailer');
        $mailer->getSwiftMailerManager()->shouldReceive('setDefaultMailer')->with($swift)->once()->andReturn(null);
        $mailer->setSwiftMailer($swift);
    }

    public function testSetSwiftMailerWithDriverName()
    {
        $mailer = $this->getMailer();
        $swift = 'foo';
        $mailer->getSwiftMailerManager()->shouldReceive('setDefaultMailer')->with($swift)->once()->andReturn(null);
        $mailer->setSwiftMailer($swift);
    }

    public function testMailerSendSendsMessageWithProperViewContent()
    {
        $mailer = $this->getMailer();
        $this->setView($mailer);
        $mailer->getSwiftMailerManager()->shouldReceive('mailer')->once()->with(null)->andReturn($this->getSwift());
        $mailer->send('foo', ['data'], function ($m) {
        });
    }

    public function testSendingMessageHandlerIsCalled()
    {
        $mailer = $this->getMailer();
        $this->setView($mailer);
        $mailer->getSwiftMailerManager()->shouldReceive('mailer')->once()->with('fooDriver')->andReturn($this->getSwift());
        $container = m::mock('Illuminate\Contracts\Container\Container');
        $container->shouldReceive('call')->once()->andReturn('fooDriver');
        $mailer->setContainer($container);
        $mailer->registerSendingMessageHandler(function () {
        });
        $mailer->send('foo', ['data'], function ($m) {
        });
    }

    public function testSendingMessageHandlerIsCalledWithProperParameters()
    {
        $mailer = $this->getMockBuilder(Mailer::class)->setMethods(['createMessage'])->setConstructorArgs($this->getMocks())->getMock();
        $mailer->setSwiftMailerManager($manager = m::mock('ElfSundae\Multimail\SwiftMailerManager'));
        $manager->shouldReceive('mailer')->once()->with('fooDriver')->andReturn($this->getSwift());
        $message = m::mock('Swift_Mime_Message');
        $mailer->expects($this->once())->method('createMessage')->will($this->returnValue($message));
        $view = m::mock('StdClass');
        $mailer->getViewFactory()->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $message->shouldReceive('setBody')->once()->with('rendered.view', 'text/html');
        $message->shouldReceive('getSwiftMessage')->once()->andReturn($message);
        $callback = function () {
        };
        $mailer->registerSendingMessageHandler($callback);
        $container = m::mock('Illuminate\Contracts\Container\Container');
        $container->shouldReceive('call')->once()->with($callback, [$message, $mailer])->andReturn('fooDriver');
        $mailer->setContainer($container);
        $mailer->send('foo', ['data'], function ($m) {
        });
    }

    protected function getMailer()
    {
        return (new Mailer(m::mock('Illuminate\Contracts\View\Factory'), m::mock('Swift_Mailer')))
            ->setSwiftMailerManager(m::mock('ElfSundae\Multimail\SwiftMailerManager'));
    }

    protected function getMocks()
    {
        return [m::mock('Illuminate\Contracts\View\Factory'), m::mock('Swift_Mailer')];
    }

    protected function setView($mailer)
    {
        $view = m::mock('StdClass');
        $mailer->getViewFactory()->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
    }

    protected function getSwift()
    {
        $swift = m::mock('Swift_Mailer');
        $swift->shouldReceive('send')->once()->andReturn(null);
        $swift->shouldReceive('getTransport')->andReturn($transport = m::mock('Swift_Transport'));
        $transport->shouldReceive('stop');

        return $swift;
    }
}
