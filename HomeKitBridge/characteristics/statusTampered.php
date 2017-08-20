<?

class HAPCharacteristicStatusTampered extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x7A,
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
