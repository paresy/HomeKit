<?

class HAPCharacteristicIdentify extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x14,
            HAPCharacteristicFormat::Boolean,
            Array(
                HAPCharacteristicPermission::PairedWrite
            )
        );
    }

}