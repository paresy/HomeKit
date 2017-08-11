<?

class HAPCharacteristicCurrentTemperature extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x11,
            HAPCharacteristicFormat::Float,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ),
            0,
            100,
            0.1,
            HAPCharacteristicUnit::Celsius
        );
    }

}