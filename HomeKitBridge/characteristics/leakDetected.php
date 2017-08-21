<?

class HAPCharacteristicLeakDetected extends HAPCharacteristic {

  const LeakNotDetacted = 0;
  const LeakDetacted = 1;

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
