<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbExpert extends HAPAccessoryLightbulbSwitch
{
    use HelperSwitchDevice;
    use HelperDimDevice;

    public function notifyCharacteristicOn()
    {
        return [
            $this->data['StateID']
        ];
    }

    public function readCharacteristicOn()
    {
        return GetValue($this->data['StateID']);
    }

    public function writeCharacteristicOn($value)
    {
        return self::switchDevice($this->data['StateID'], $value);
    }

    public function notifyCharacteristicBrightness()
    {
        return [
            $this->data['BrightnessID']
        ];
    }

    public function readCharacteristicBrightness()
    {
        return self::getDimValue($this->data['BrightnessID']);
    }

    public function writeCharacteristicBrightness($value)
    {
        return self::dimDevice($this->data['BrightnessID'], $value);
    }
}

class HAPAccessoryConfigurationLightbulbExpert extends HAPAccessoryConfigurationLightbulbSwitch
{
    use HelperDimDevice;
    use HelperSwitchDevice;

    public static function getPosition()
    {
        return 4;
    }

    public static function getCaption()
    {
        return 'Lightbulb (Expert)';
    }

    public static function getObjectIDs($data)
    {
        return [
            $data['StateID'],
            $data['BrightnessID'],
        ];
    }

    public static function getStatus($data)
    {
        if (!IPS_VariableExists($data['StateID'])) {
            return 'Variable StateID missing';
        }

        if (!IPS_VariableExists($data['BrightnessID'])) {
            return 'Variable BrightnessID missing';
        }

        $status = self::getSwitchCompatibility($data['StateID']);
        if ($status != 'OK') {
            return $status;
        }

        return self::getDimCompatibility($data['BrightnessID']);
    }

    public static function getColumns()
    {
        return [
            [
                'caption' => 'StateID',
                'name'    => 'StateID',
                'width'   => '250px',
                'add'     => 0,
                'edit'    => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'caption' => 'BrightnessID',
                'name'    => 'BrightnessID',
                'width'   => '250px',
                'add'     => 0,
                'edit'    => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Lightbulb (Expert)'               => 'Lampe (Experte)',
                'StateID'                          => 'StatusID',
                'BrightnessID'                     => 'HelligkeitsID',
                'Variable StateID missing'         => 'Variable StatusID fehlt',
                'Variable BrightnessID missing'    => 'Variable HelligkeitsID fehlt',
                'Integer/Float required'               => 'Integer/Float benötigt',
                'Profile required'                 => 'Profil benötigt',
                'Action required'                  => 'Aktion benötigt',
                'OK'                               => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbExpert');
