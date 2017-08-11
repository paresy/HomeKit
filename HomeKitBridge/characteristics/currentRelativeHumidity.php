<?

class HAPCharacteristicCurrentRelativeHumidity extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x10,
            HAPCharacteristicFormat::Float,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ),
            0,
            100,
            1,
            HAPCharacteristicUnit::Percentage
        );
    }

}