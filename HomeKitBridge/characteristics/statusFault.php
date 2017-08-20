<?

class HAPCharacteristicStatusFault extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x77,
            HAPCharacteristicFormat::UnsignedInt8,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ),
            0,
            1,
            1
        );
    }

}
