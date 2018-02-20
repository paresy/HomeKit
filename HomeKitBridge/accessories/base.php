<?php

declare(strict_types=1);

class HAPAccessoryBase extends HAPAccessory
{
    public function writeCharacteristicIdentify($value)
    {

        //TODO: We probably should send some event
    }

    public function readCharacteristicManufacturer()
    {
        return 'IP-Symcon Community';
    }

    public function readCharacteristicModel()
    {
        return str_replace('HAPAccessory', '', get_class($this));
    }

    public function readCharacteristicName()
    {
        if (isset($this->data['Name'])) {
            return $this->data['Name'];
        } else {
            return 'Undefined';
        }
    }

    public function readCharacteristicSerialNumber()
    {
        return substr(IPS_GetKernelRevision(), 1, 8);
    }

    public function readCharacteristicFirmwareRevision()
    {
        return IPS_GetKernelVersion();
    }
}
