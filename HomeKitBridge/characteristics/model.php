<?

class HAPCharacteristicModel extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x21,
            HAPCharacteristicFormat::String,
            Array(
                HAPCharacteristicPermission::PairedRead
            )
        );
    }

}