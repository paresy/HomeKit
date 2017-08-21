<?

class HAPCharacteristicStatusLowBattery extends HAPCharacteristic {

  const BatteryLevelNormal = 0;
  const BatteryLevelLow = 1;

    public function __construct()
    {
        parent::__construct(
            0x79,
            HAPCharacteristicFormat::UnsignedInt8,
            Array(
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ),
            0,
            1,
            1
        );
    }

}
