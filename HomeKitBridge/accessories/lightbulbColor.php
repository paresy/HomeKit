<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbColor extends HAPAccessoryLightbulbSwitch
{
    public function notifyCharacteristicOn()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicOn()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicOn($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function notifyCharacteristicHue()
    {
        return GetValue($this->data['VariableID']);
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
        return GetValue($this->data['VariableID']);
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
        return GetValue($this->data['VariableID']);
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
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'Variable missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Int required';
        }

        if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }

        if ($profileName != '~HexColor') {
            return 'HexColor required';
        }

        if ($targetVariable['VariableCustomAction'] != '') {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'Action required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            "de" => [
                "Lightbulb (Color)"     => "Lampe (Farbig)",
                "VariableID"            => "VariablenID",
                "Variable missing"      => "Variable fehlt",
                "Int required"          => "Int benötigt",
                "HexColor required"     => "HexColor benötigt",
                "Action required"       => "Aktion benötigt",
                "OK"                    => "OK"
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbColor');
