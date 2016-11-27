<?php

namespace ElfSundae\Multimail;

use Illuminate\Mail\TransportManager as BaseTransportManager;

class TransportManager extends BaseTransportManager
{
    /**
     * Reset a Transport driver instance.
     *
     * @param  string  $name
     */
    public function resetDriver($name)
    {
        unset($this->drivers[$name]);
    }

    /**
     * Reset all of the created Transport instance.
     */
    public function resetDrivers()
    {
        $this->drivers = [];
    }
}
