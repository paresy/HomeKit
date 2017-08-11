<?

class HAPCharacteristicOn extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x25,
            HAPCharacteristicFormat::Boolean,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            )
        );
    }

}