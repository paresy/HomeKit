<?php

class HAPCharacteristicFilterChangeIndication extends HAPCharacteristic
{
    const FilterDoesNotBeChanged = 0;
    const FilterNeedsToBeChanged = 1;

    public function __construct()
    {
        parent::__construct(
            0xAC,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
