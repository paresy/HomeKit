<?

class HAPCharacteristicLeakDetected extends HAPCharacteristic {

  const LeakNotDetected = 0;
  const LeakDetected = 1;

    public function __construct()
    {
        parent::__construct(
            0x70,
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
