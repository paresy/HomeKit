<?php

declare(strict_types=1);

class HAPAccessoryBase extends HAPAccessory
{
    public function setCharacteristicIdentify($value)
    {

        //TODO: We probably should send some event
    }

    public function getCharacteristicManufacturer()
    {
        return 'IP-Symcon Community';
    }

    public function getCharacteristicModel()
    {
        return str_replace('HAPAccessory', '', get_class($this));
    }

    public function getCharacteristicName()
    {
        if (isset($this->data['Name'])) {
            return $this->data['Name'];
        } else {
            return 'Undefined';
        }
    }

    public function getCharacteristicSerialNumber()
    {
        return 'Undefined';
    }
}
