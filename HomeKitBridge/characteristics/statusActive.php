<?

class HAPCharacteristicStatusActive extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x75,
            HAPCharacteristicFormat::Boolean,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            )
        );
    }

}
