<?php

use ElfSundae\Multimail\MessageHelper;
use Mockery as m;

class MessageHelperTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testGetRecipients()
    {
        $message = m::mock('StdClass');
        $message->shouldReceive('getTo', 'getReplyTo', 'getCc', 'getBcc')
            ->andReturn(['foo@example.com' => 'Bar']);

        $this->assertEquals(['foo@example.com'], MessageHelper::getRecipients($message));
        $this->assertEquals(['foo@example.com' => 'Bar'], MessageHelper::getRecipients($message, true));
    }

    public function testGetRecipientsDomains()
    {
        $message = m::mock('StdClass');
        $message->shouldReceive('getTo', 'getReplyTo', 'getCc', 'getBcc')
            ->andReturn(['foo@EXAMPLE.com' => 'Bar']);

        $this->assertEquals(['example.com'], MessageHelper::getRecipientsDomains($message));
    }
}
