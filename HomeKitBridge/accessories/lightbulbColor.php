<?php

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbColor extends HAPAccessoryLightbulbSwitch
{
    public function getCharacteristicOn()
    {
        return GetValue($this->data['VariableID']);
    }

    public function setCharacteristicOn($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function getCharacteristicHue()
    {
        return GetValue($this->data['VariableID']);
    }

    public function setCharacteristicHue($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function getCharacteristicSaturation()
    {
        return GetValue($this->data['VariableID']);
    }

    public function setCharacteristicSaturation($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function getCharacteristicColorTemperature()
    {
        return GetValue($this->data['VariableID']);
    }

    public function setCharacteristicColorTemperature($value)
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
