<?php

namespace ElfSundae\Multimail;

use Closure;
use Illuminate\Support\Manager;
use Swift_Mailer;
use Swift_Message;

class SwiftMailerManager extends Manager
{
    /**
     * The Transport manager.
     *
     * @var \ElfSundae\Multimail\TransportManager
     */
    protected $transportManager;

    /**
     * The registered custom driver selector.
     *
     * @var \Closure|string
     */
    protected $driverSelector;

    /**
     * Get the Transport manager.
     *
     * @return \ElfSundae\Multimail\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the Transport manager.
     *
     * @param  \ElfSundae\Multimail\TransportManager  $manager
     * @return $this
     */
    public function setTransportManager(TransportManager $manager)
    {
        $this->transportManager = $manager;

        return $this;
    }

    /**
     * Get a Swift Mailer instance.
     *
     * @param  string|null  $driver
     * @return \Swift_Mailer
     */
    public function mailer($driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Get a Swift Mailer instance for the given message.
     *
     * @param  \Swift_Message  $message
     * @return \Swift_Mailer
     */
    public function mailerForMessage(Swift_Message $message)
    {
        $driver = $this->callDriverSelector($message, $this);

        if ($driver instanceof Swift_Mailer) {
            return $driver;
        }

        return $this->mailer($driver);
    }

    /**
     * Get all of the created Swift Mailer instances.
     *
     * @return array
     */
    public function getMailers()
    {
        return $this->drivers;
    }

    /**
     * Get the name of mail driver for the given Swift Mailer instance.
     *
     * @param  \Swift_Mailer  $mailer
     * @return string|null
     */
    public function getDriverForMailer(Swift_Mailer $mailer)
    {
        return array_search($mailer, $this->drivers) ?: null;
    }

    /**
     * Reset a Swift Mailer instance.
     *
     * @param  string|\Swift_Mailer  $mailer
     * @return $this
     */
    public function resetMailer($mailer)
    {
        if ($driver = $this->validDriverName($mailer)) {
            unset($this->drivers[$driver]);
            $this->transportManager->resetDriver($driver);
        }

        return $this;
    }

    /**
     * Reset all of the created Swift Mailer instances.
     *
     * @return $this
     */
    public function resetMailers()
    {
        $this->drivers = [];
        $this->transportManager->resetDrivers();

        return $this;
    }

    /**
     * Validate the given driver or mailer, and return the driver name.
     *
     * @param  mixed  $driver
     * @return string|null
     */
    protected function validDriverName($driver)
    {
        if ($driver instanceof Swift_Mailer) {
            $driver = $this->getDriverForMailer($driver);
        }

        if (is_string($driver)) {
            return $driver;
        }
    }

    /**
     * Create a new Swift Mailer instance.
     *
     * @param  string  $driver
     * @return \Swift_Mailer
     */
    protected function createDriver($driver)
    {
        return new Swift_Mailer($this->transportManager->driver($driver));
    }

    /**
     * Get the default mail driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->transportManager->getDefaultDriver();
    }

    /**
     * Set the default mail driver name.
     *
     * @param  string  $driver
     * @return $this
     */
    public function setDefaultDriver($driver)
    {
        $this->transportManager->setDefaultDriver($driver);

        return $this;
    }

    /**
     * Set the default Swift Mailer.
     *
     * @param  string|\Swift_Mailer  $mailer
     * @return $this
     */
    public function setDefaultMailer($mailer)
    {
        if ($driver = $this->validDriverName($mailer)) {
            $this->setDefaultDriver($driver);
        }

        return $this;
    }

    /**
     * Register the custom driver selector.
     *
     * @param  \Closure|string  $selector
     * @return $this
     */
    public function registerDriverSelector($selector)
    {
        $this->driverSelector = $selector;
    }

    /**
     * Call the custom driver selector.
     *
     * @param  mixed  ...$args
     * @return mixed
     */
    protected function callDriverSelector(...$args)
    {
        if ($this->driverSelector instanceof Closure) {
            return call_user_func($this->driverSelector, ...$args);
        }

        if (is_string($this->driverSelector)) {
            return $this->app->make($this->driverSelector)->mailDriver(...$args);
        }
    }
}
