<?php

declare(strict_types=1);

class HAPAccessorySpeaker extends HAPAccessoryBase
{
    use HelperSwitchDevice;

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
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicMute()
    {
        switch (GetValue($this->data['VariableID'])) {
            case 0:
                return HAPCharacteristicMute::MuteOn;
            case 1:
                return HAPCharacteristicMute::MuteOff;
        }
        return HAPCharacteristicMute::MuteOff;
    }

    public function writeCharacteristicMute($value)
    {
		 switch ($value) {
            case HAPCharacteristicMute::MuteOn:
                $value = 0;
                break;
            case HAPCharacteristicMute::MuteOff:
                $value = 1;
                break;
        }

        self::switchDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationSpeaker
{
    use HelperSwitchDevice;

    public static function getPosition()
    {
        return 93;
    }

    public static function getCaption()
    {
        return 'Speaker';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
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
        return self::getSwitchCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Speaker'               => 'Lautsprecher',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Bool required'         => 'Bool benötigt',
                'Action required'       => 'Aktionsscript benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Speaker');
