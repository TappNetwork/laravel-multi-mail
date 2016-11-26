<?php

namespace ElfSundae\Multimail;

use Illuminate\Support\Manager;
use Swift_Mailer;

class SwiftMailerManager extends Manager
{
    /**
     * The Transport manager.
     *
     * @var \ElfSundae\Multimail\TransportManager
     */
    protected $transportManager;

    /**
     * The default driver.
     *
     * @var string
     */
    protected $defaultDriver;

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
        return $this->defaultDriver ?: $this->transportManager->getDefaultDriver();
    }

    /**
     * Set the default mail driver name.
     *
     * @param  string  $driver
     * @return $this
     */
    public function setDefaultDriver($driver)
    {
        $this->defaultDriver = $driver;

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
}
