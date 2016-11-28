<?php

use ElfSundae\Multimail\SwiftMessageHelper;
use Mockery as m;

class SwiftMessageHelperTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testGetRecipients()
    {
        $message = m::mock('Swift_Mime_Message');
        $message->shouldReceive('getTo', 'getReplyTo', 'getCc', 'getBcc')
            ->andReturn(['foo@example.com' => 'Bar']);

        $this->assertEquals(['foo@example.com'], SwiftMessageHelper::getRecipients($message));
        $this->assertEquals(['foo@example.com' => 'Bar'], SwiftMessageHelper::getRecipients($message, true));
    }

    public function testGetRecipientsDomains()
    {
        $message = m::mock('Swift_Mime_Message');
        $message->shouldReceive('getTo', 'getReplyTo', 'getCc', 'getBcc')
            ->andReturn(['foo@EXAMPLE.com' => 'Bar']);

        $this->assertEquals(['example.com'], SwiftMessageHelper::getRecipientsDomains($message));
    }
}
