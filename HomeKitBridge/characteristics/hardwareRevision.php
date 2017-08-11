<?

class HAPCharacteristicHardwareRevision extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x23,
            HAPCharacteristicFormat::String,
            Array(
                HAPCharacteristicPermission::PairedRead
            )
        );
    }

}