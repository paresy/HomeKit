<?php

declare(strict_types=1);

class HAPAccessoryBridge extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation()
            ]
        );
    }
}
