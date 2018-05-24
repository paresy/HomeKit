<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbDimmer extends HAPAccessoryLightbulbSwitch
{
    use HelperDimDevice;

    public function notifyCharacteristicOn()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicOn()
    {
        return self::getDimValue($this->data['VariableID']) > 0;
    }

    public function writeCharacteristicOn($value)
    {
        //Only switch the device on, if it isn't on.
        //This should fix the problem that Apple sends on before dimming
        if ($value && $this->readCharacteristicOn()) {
            return;
        }

        if ($value) {
            self::dimDevice($this->data['VariableID'], 100);
        } else {
            self::dimDevice($this->data['VariableID'], 0);
        }
    }

    public function notifyCharacteristicBrightness()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicBrightness()
    {
        return self::getDimValue($this->data['VariableID']);
    }

    public function writeCharacteristicBrightness($value)
    {
        self::dimDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationLightbulbDimmer extends HAPAccessoryConfigurationLightbulbSwitch
{
    use HelperDimDevice;

    public static function getPosition()
    {
        return 2;
    }

    public static function getCaption()
    {
        return 'Lightbulb (Dimmer)';
    }

    public static function getStatus($data)
    {
        return self::getDimCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Lightbulb (Dimmer)'    => 'Lampe (Dimmbar)',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int/Float required'    => 'Int/Float benötigt',
                'Profile required'      => 'Profil benötigt',
                'Action required'       => 'Aktion benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbDimmer');
