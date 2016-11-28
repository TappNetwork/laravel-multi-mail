<?php

namespace ElfSundae\Multimail;

use Closure;
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
            return $this->container->call($this->mailDriverHandler, $args);
        }

        if (is_string($this->mailDriverHandler)) {
            return $this->container->call($this->mailDriverHandler, $args, 'mailDriver');
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
     * Send a new message using a view.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     */
    public function send($view, array $data = [], $callback = null)
    {
        if ($view instanceof MailableContract) {
            return $view->send($this);
        }

        // First we need to parse the view, which could either be a string or an array
        // containing both an HTML and plain text versions of the view which should
        // be used when sending an e-mail. We will extract both of them out here.
        list($view, $plain, $raw) = $this->parseView($view);

        $data['message'] = $message = $this->createMessage();

        // Once we have retrieved the view content for the e-mail we will set the body
        // of this message using the HTML type, which will provide a simple wrapper
        // to creating view based emails that are able to receive arrays of data.
        $this->addContent($message, $view, $plain, $raw, $data);

        $this->callMessageBuilder($callback, $message);

        if (isset($this->to['address'])) {
            $message->to($this->to['address'], $this->to['name'], true);
        }

        $swift = $this->getSwiftMailerForMessage($message);

        $message = $message->getSwiftMessage();

        $this->sendSwiftMessage($message, $swift);
    }

    /**
     * Send a Swift Message instance.
     *
     * @param  \Swift_Message  $message
     * @param  \Swift_Mailer  $swift
     */
    protected function sendSwiftMessage($message, Swift_Mailer $swift = null)
    {
        if ($this->events) {
            $this->events->fire(new MessageSending($message));
        }

        if (is_null($swift)) {
            $swift = $this->getSwiftMailer();
        }

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
     * @param  \Swift_Mailer  $swift
     */
    protected function forceReconnection(Swift_Mailer $swift = null)
    {
        if (is_null($swift)) {
            $swift = $this->getSwiftMailer();
        }

        $swift->getTransport()->stop();
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
     * @param  string|\Swift_Mailer  $swift
     */
    public function setSwiftMailer($swift)
    {
        $this->swiftManager->setDefaultMailer($swift);

        // Our $swift is managed by the SwiftMailerManager singleton,
        // so just let $this->swift go.
    }
}
