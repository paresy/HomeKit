<?

class HAPCharacteristicLockCurrentState extends HAPCharacteristic {

    const Unsecured = 0;
    const Secured = 1;
    const Jammed = 2;
    const Unknown = 3;

    public function __construct()
    {
        parent::__construct(
            0x1D,
            HAPCharacteristicFormat::UnsignedInt8,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ),
            0,
            3,
            1
        );
    }

}