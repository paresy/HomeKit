<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbColor extends HAPAccessoryLightbulbSwitch
{
    use HelperColorDevice;

    public function notifyCharacteristicOn()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicOn()
    {
        return self::getColorValue($this->data['VariableID']) > 0;
    }

    public function writeCharacteristicOn($value)
    {
        self::colorDevice($this->data['VariableID'], 0xFFFFFF);
    }

    public function notifyCharacteristicBrightness()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicBrightness()
    {
        return self::getColorBrightness($this->data['VariableID']);
    }

    public function writeCharacteristicBrightness($value)
    {
        self::setColorBrightness($this->data['VariableID'], $value);
    }

    public function notifyCharacteristicHue()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicHue()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicHue($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function notifyCharacteristicSaturation()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicSaturation()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicSaturation($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function notifyCharacteristicColorTemperature()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicColorTemperature()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicColorTemperature($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationLightbulbColor extends HAPAccessoryConfigurationLightbulbSwitch
{
    use HelperColorDevice;

    public static function getPosition()
    {
        return 3;
    }

    public static function getCaption()
    {
        return 'Lightbulb (Color)';
    }

    public static function getStatus($data)
    {
        return self::getColorCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Lightbulb (Color)'     => 'Lampe (Farbig)',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int required'          => 'Int benötigt',
                'HexColor required'     => 'HexColor benötigt',
                'Action required'       => 'Aktion benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbColor');
