<?php

class HAPCharacteristicServiceLabelIndex extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xCB,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead
            ],
            1, /*Minimum Value */
            null,
            1 /* Step Value */
        );
    }
}
