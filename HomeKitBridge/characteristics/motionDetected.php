<?

class HAPCharacteristicMotionDetected extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x22,
            HAPCharacteristicFormat::Boolean,
            Array(
                HAPCharacteristicPermission::PairedRead,
            ),
        );
    }

}
