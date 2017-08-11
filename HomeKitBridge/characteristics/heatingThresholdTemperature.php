<?

class HAPCharacteristicHeatingThresholdTemperature extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x12,
            HAPCharacteristicFormat::Float,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ),
            10,
            25,
            0.1,
            HAPCharacteristicUnit::Celsius
        );
    }

}