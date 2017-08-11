<?

class HAPCharacteristicAdministratorOnlyAccess extends HAPCharacteristic {

    public function __construct()
    {
        parent::__construct(
            0x01,
            HAPCharacteristicFormat::Boolean,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            )
        );
    }

}