<?php

namespace ElfSundae\Multimail;

use Illuminate\Contracts\Mail\Mailable as MailableContract;
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
     * The registered mail driver handler.
     *
     * @var \Closure|string
     */
    protected $mailDriverHandler;

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
     * Register the mail driver handler.
     *
     * @param  \Closure|string  $handler
     * @return $this
     */
    public function registerMailDriverHandler($handler)
    {
        $this->mailDriverHandler = $handler;

        return $this;
    }

    /**
     * Call the registered mail driver handler.
     *
     * @param  mixed  ...$args
     * @return mixed
     */
    protected function callMailDriverHandler(...$args)
    {
        if ($this->mailDriverHandler instanceof Closure) {
            return call_user_func($this->mailDriverHandler, ...$args);
        }

        if (is_string($this->mailDriverHandler)) {
            return $this->app->make($this->mailDriverHandler)->mailDriver(...$args);
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
        $swift = $this->callMailDriverHandler($message, $this);

        if ($swift instanceof Swift_Mailer) {
            return $swift;
        }

        return $this->swiftManager->mailer($swift);
    }

    /**
     * Send a Swift Message instance.
     *
     * @param  \Swift_Message  $message
     * @return void
     */
    protected function sendSwiftMessage($message)
    {
        if ($this->events) {
            $this->events->fire(new MessageSending($message));
        }

        $swift = $this->swiftManager->mailerForMessage($message);

        try {
            return $swift->send($message, $this->failedRecipients);
        } finally {
            $this->forceReconnection($swift);
        }
    }

    /**
     * Force the transport to re-connect.
     *
     * This will prevent errors in daemon queue situations.
     *
     * @param  \Swift_Mailer  $swiftMailer
     * @return void
     */
    protected function forceReconnection($swiftMailer = null)
    {
        if (is_null($swiftMailer)) {
            $swiftMailer = $this->getSwiftMailer();
        }

        $swiftMailer->getTransport()->stop();
    }

    /**
     * Get the Swift Mailer instance.
     *
     * @return \Swift_Mailer
     */
    public function getSwiftMailer()
    {
        return $this->swiftManager->mailer();
    }

    /**
     * Set the Swift Mailer instance.
     *
     * @param  \Swift_Mailer  $swift
     * @return void
     */
    public function setSwiftMailer($swift)
    {
        $this->swiftManager->setDefaultMailer($swift);

        // Our $swift is managed by the SwiftMailerManager singleton,
        // so just let $this->swift go.
    }
}
