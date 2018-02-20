<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbColor extends HAPAccessoryLightbulbSwitch
{
    public function readCharacteristicOn()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicOn($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function readCharacteristicHue()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicHue($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function readCharacteristicSaturation()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicSaturation($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
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
}

//HomeKitManager::registerAccessory("LightbulbColor");
