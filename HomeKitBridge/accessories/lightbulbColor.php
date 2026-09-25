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
        return $this->readCharacteristicBrightness() > 0;
    }

    public function writeCharacteristicOn($value)
    {
        if ($value) {
            $this->writeCharacteristicBrightness(100);
        } else {
            $this->writeCharacteristicBrightness(0);
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
        $hsb = self::rgbToHSB(GetValue($this->data['VariableID']));
        return $hsb['brightness'] * 100;
    }

    public function writeCharacteristicBrightness($value)
    {
        $hsb = self::rgbToHSB(GetValue($this->data['VariableID']));
        $hsb['brightness'] = $value / 100;
        self::colorDevice($this->data['VariableID'], self::hsbToRGB($hsb));
    }

    public function notifyCharacteristicHue()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicHue()
    {
        $hsb = self::rgbToHSB(GetValue($this->data['VariableID']));
        return $hsb['hue'];
    }

    public function writeCharacteristicHue($value)
    {
        $hsb = self::rgbToHSB(GetValue($this->data['VariableID']));
        $hsb['hue'] = $value;
        self::colorDevice($this->data['VariableID'], self::hsbToRGB($hsb));
    }

    public function notifyCharacteristicSaturation()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicSaturation()
    {
        $hsb = self::rgbToHSB(GetValue($this->data['VariableID']));
        return $hsb['saturation'] * 100;
    }

    public function writeCharacteristicSaturation($value)
    {
        $hsb = self::rgbToHSB(GetValue($this->data['VariableID']));
        $hsb['saturation'] = $value / 100;
        self::colorDevice($this->data['VariableID'], self::hsbToRGB($hsb));
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

    public static function getObjectIDs($data)
    {
        return [
            $data['VariableID'],
        ];
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
