<?

class HAPCharacteristicSmokeDetected extends HAPCharacteristic {

  const SmokeNotDetected = 0;
  const SmokeDetected = 1;

    public function __construct()
    {
        parent::__construct(
            0x76,
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
