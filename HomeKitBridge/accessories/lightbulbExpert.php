<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbExpert extends HAPAccessoryLightbulbSwitch
{
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
        //Only switch the device on, if it isn't on.
        //This should fix the problem that Apple sends on before dimming
        if ($value && $this->readCharacteristicOn()) {
            return;
        }

        if ($value) {
            self::dimDevice($this->data['BrightnessID'], self::getDimValue($this->data['BrightnessID']));
        } else {
            self::dimDevice($this->data['BrightnessID'], 0);
        }
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
        self::dimDevice($this->data['BrightnessID'], $value);
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

    public static function getStatus($data)
    {
        if (!IPS_VariableExists($data['StateID'])) {
            return 'Variable StateID missing';
        }

        if (!IPS_VariableExists($data['BrightnessID'])) {
            return 'Variable BrigntessID missing';
        }

        return self::getSwitchCompatibility($data['StateID']);
        return self::getDimCompatibility($data['BrightnessID']);
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'StateID',
                'name'  => 'StateID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'BrightnessID',
                'name'  => 'BrightnessID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
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
                'Int/Float required'               => 'Int/Float benötigt',
                'Profile required'                 => 'Profil benötigt',
                'Action required'                  => 'Aktion benötigt',
                'OK'                               => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbExpert');
