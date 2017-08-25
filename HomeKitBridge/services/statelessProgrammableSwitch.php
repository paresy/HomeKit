<?php

declare(strict_types=1);
class HAPServiceStatelessProgrammableSwitch extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x89,
            [
                //Required Characteristics
                new HAPCharacteristicProgrammableSwitchEvent()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicServiceLabelIndex()
            ]
        );
    }
}
