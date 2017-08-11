<?

class HAPCharacteristicAudioFeedback extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x05,
            HAPCharacteristicFormat::Boolean,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            )
        );
    }

}