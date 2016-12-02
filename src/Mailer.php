<?php

namespace ElfSundae\Multimail;

use Closure;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Mailer as BaseMailer;
use Swift_Mailer;

class Mailer extends BaseMailer
{
    /**
     * The Swift Mailer Manager instance.
     *
     * @var \ElfSundae\Multimail\SwiftMailerManager
     */
    protected $swiftManager;

    /**
     * The registered handler of sending message.
     *
     * @var \Closure|string
     */
    protected $sendingMessageHandler;

    /**
     * Get the Swift Mailer Manager instance.
     *
     * @return \ElfSundae\Multimail\SwiftMailerManager
     */
    public function getSwiftMailerManager()
    {
        return $this->swiftManager;
    }

    /**
     * Set the Swift Mailer Manager instance.
     *
     * @param  \ElfSundae\Multimail\SwiftMailerManager  $manager
     * @return $this
     */
    public function setSwiftMailerManager(SwiftMailerManager $manager)
    {
        $this->swiftManager = $manager;

        return $this;
    }

    /**
     * Register handler of sending message.
     *
     * @param  \Closure|string  $handler
     * @return $this
     */
    public function registerSendingMessageHandler($handler)
    {
        $this->sendingMessageHandler = $handler;

        return $this;
    }

    /**
     * Call the registered handler of sending message.
     *
     * @param  mixed  ...$args
     * @return mixed
     */
    protected function callSendingMessageHandler(...$args)
    {
        if ($this->sendingMessageHandler instanceof Closure) {
            return $this->container->call($this->sendingMessageHandler, $args);
        }

        if (is_string($this->sendingMessageHandler)) {
            return $this->container->call($this->sendingMessageHandler, $args, 'sendingMail');
        }
    }

    /**
     * Get a Swift Mailer instance for the given message.
     *
     * @param  mixed  $message
     * @return \Swift_Mailer
     */
    protected function getSwiftMailerForMessage($message)
    {
        $driver = $this->callSendingMessageHandler($message, $this);

        if ($driver instanceof Swift_Mailer) {
            return $driver;
        }

        return $this->getSwiftMailer($driver);
    }

    /**
     * Send a Swift Message instance.
     *
     * @param  \Swift_Message  $message
     */
    protected function sendSwiftMessage($message)
    {
        if ($this->events) {
            $this->events->fire(new MessageSending($message));
        }

        $swift = $this->getSwiftMailerForMessage($message);

        try {
            return $swift->send($message, $this->failedRecipients);
        } finally {
            $swift->getTransport()->stop();
        }
    }

    /**
     * Get the Swift Mailer instance.
     *
     * @param  string|null  $driver
     * @return \Swift_Mailer
     */
    public function getSwiftMailer($driver = null)
    {
        return $this->swiftManager->mailer($driver);
    }

    /**
     * Set the Swift Mailer instance.
     *
     * @param  string|\Swift_Mailer  $swift
     * @return $this
     */
    public function setSwiftMailer($swift)
    {
        $this->swiftManager->setDefaultMailer($swift);

        return $this;
    }

    /**
     * Set the mail driver.
     *
     * @param  string  $driver
     * @return $this
     */
    public function mailDriver($driver)
    {
        $this->swiftManager->setDefaultDriver($driver);

        return $this;
    }
}
