<?

class HAPCharacteristicCurrentHeatingCoolingState extends HAPCharacteristic {

    const Off = 0;
    const Heat = 1;
    const Cool = 2;

    public function __construct()
    {
        parent::__construct(
            0x0F,
            HAPCharacteristicFormat::UnsignedInt8,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ),
            0,
            2,
            1
        );
    }

}