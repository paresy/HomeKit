<?php

declare(strict_types=1);

class HAPAccessoryBridge extends HAPAccessoryBase
{
    public function __construct()
    {
        parent::__construct(
            [],
            [
                new HAPServiceAccessoryInformation()
            ]
        );
    }
}
