<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbDimmer extends HAPAccessoryLightbulbSwitch
{
    public function readCharacteristicOn()
    {
        $profile = $this->getProfile();

        return GetValue($this->data['VariableID']) > $profile['MinValue'];
    }

    public function writeCharacteristicOn($value)
    {
        $profile = $this->getProfile();

        //Only switch the device on, if it isn't on.
        //This should fix the problem that Apple sends on before dimming
        if ($value && $this->readCharacteristicOn()) {
            return;
        }

        if ($value) {
            $value = $profile['MaxValue'];
        } else {
            $value = $profile['MinValue'];
        }

        $this->switchDevice($this->data['VariableID'], $value);
    }

    public function readCharacteristicBrightness()
    {
        $profile = $this->getProfile();

        $valueToPercent = function ($value) use ($profile) {
            return (($value - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue'])) * 100;
        };

        return $valueToPercent(GetValue($this->data['VariableID']));
    }

    public function writeCharacteristicBrightness($value)
    {
        $profile = $this->getProfile();

        $percentToValue = function ($value) use ($profile) {
            return ($value / 100) * ($profile['MaxValue'] - $profile['MinValue']) + $profile['MinValue'];
        };

        $this->switchDevice($this->data['VariableID'], $percentToValue($value));
    }

    private function getProfile()
    {
        $targetVariable = IPS_GetVariable($this->data['VariableID']);

        if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }

        return IPS_GetVariableProfile($profileName);
    }
}

class HAPAccessoryConfigurationLightbulbDimmer extends HAPAccessoryConfigurationLightbulbSwitch
{
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
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'Variable missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */ && $targetVariable['VariableType'] != 2 /* Float */) {
            return 'Int/Float required';
        }

        if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }

        if ($profileName == '' || !IPS_VariableProfileExists($profileName)) {
            return 'Profile required';
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

HomeKitManager::registerAccessory('LightbulbDimmer');
