<?php

declare(strict_types=1);

class HAPAccessoryBridge extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceProtocolInformation(),
            ]
        );
    }

    public function readCharacteristicVersion()
    {
        return "01.01.00";
    }
}
