<?

class HAPCharacteristicHue extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x13,
            HAPCharacteristicFormat::Float,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ),
            0,
            360,
            1,
            HAPCharacteristicUnit::ArcDegrees
        );
    }

}