<?php

class HAPAccessoryBridge extends HAPAccessory
{
    public function __construct()
    {
        parent::__construct(
            null,
            [
                new HAPServiceAccessoryInformation()
            ]
        );
    }

    public function setCharacteristicIdentify($value)
    {

        //TODO: We probably should send some event
    }

    public function getCharacteristicManufacturer()
    {
        return 'Symcon GmbH';
    }

    public function getCharacteristicModel()
    {
        return 'HomeKit Bridge';
    }

    public function getCharacteristicName()
    {
        return 'IP-Symcon';
    }

    public function getCharacteristicSerialNumber()
    {
        return 'Undefined';
    }
}
