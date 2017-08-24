<?php

class HAPServiceCameraRTPStreamManagement extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x110,
            [
                //Required Characteristics
                new HAPCharacteristicStreamingStatus(),
                new HAPCharacteristicSupportedVideoStreamConfiguration(),
                new HAPCharacteristicSupportedAudioStreamConfiguration(),
                new HAPCharacteristicSupportedRTPConfiguration(),
                new HAPCharacteristicSetupEndpoints(),
                new HAPCharacteristicSelectedRTPStreamConfiguration()
            ],
            [
                //Optional Characteristics
            ]
        );
    }
}
