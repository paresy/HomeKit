<?php

declare(strict_types=1);

class HAPAccessorySpeaker extends HAPAccessoryBase
{
    use HelperSwitchDevice;
    use HelperDimDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceSpeaker()
            ]
        );
    }

    public function notifyCharacteristicMute()
    {
        return [
            $this->data['MuteID']
        ];
    }

    public function notifyCharacteristicVolume()
    {
        return [
            $this->data['VolumeID']
        ];
    }

    public function readCharacteristicMute()
    {
        return GetValue($this->data['MuteID']);
    }

    public function readCharacteristicVolume()
    {
        var_dump($this->data);
         return GetValue($this->data['VolumeID']);
    }

    public function writeCharacteristicMute($value)
    {
        self::switchDevice($this->data['MuteID'], $value);
    }

    public function writeCharacteristicVolume($value)
    {
        self::dimDevice($this->data['VolumeID'], $value);
    }
}

class HAPAccessoryConfigurationSpeaker
{
    use HelperSwitchDevice;
    use HelperDimDevice;

    public static function getPosition()
    {
        return 250;
    }

    public static function getCaption()
    {
        return 'Speaker';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'MuteID',
                'name'  => 'MuteID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'VolumeID',
                'name'  => 'VolumeID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getStatus($data)
    {
        var_dump($data);
        $return = self::getSwitchCompatibility($data['MuteID']);

        /* Variable ist optional */
        if (array_key_exists('VolumeID',$data)) {
            $return .= " ".self::getDimCompatibility($data['VolumeID']);
        }
        return $return;
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Speaker'               => 'Lautsprecher',
                'MuteID'                => 'MuteID',
                'VolumeID'              => 'VolumeID',
                'Variable missing'      => 'Variable fehlt',
                'Bool required'         => 'Bool benötigt',
                'Action required'       => 'Aktion benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Speaker');
