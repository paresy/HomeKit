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
        $this->colorDeviceWait($this->data['VariableID'], self::hsbToRGB($hsb));
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
        $this->colorDeviceWait($this->data['VariableID'], self::hsbToRGB($hsb));
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
        $this->colorDeviceWait($this->data['VariableID'], self::hsbToRGB($hsb));
    }

    //We need to use the sync function, because each part (Hue, Sat, Bri) will be set independently and GetValue will have wrong values (async)
    private static function colorDeviceWait($variableID, $value)
    {
        if (!IPS_VariableExists($variableID)) {
            return false;
        }

        $targetVariable = IPS_GetVariable($variableID);

        if ($targetVariable['VariableCustomAction'] != 0) {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if ($profileAction < 10000) {
            return false;
        }

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return false;
        }

        if (($value < 0) || ($value > 0xFFFFFF)) {
            return false;
        }

        if (IPS_InstanceExists($profileAction)) {
            IPS_RunScriptTextWait('IPS_RequestAction(' . var_export($profileAction, true) . ', ' . var_export(IPS_GetObject($variableID)['ObjectIdent'], true) . ', ' . var_export($value, true) . ');');
        } elseif (IPS_ScriptExists($profileAction)) {
            IPS_RunScriptWaitEx($profileAction, ['VARIABLE' => $variableID, 'VALUE' => $value, 'SENDER' => 'VoiceControl']);
        } else {
            return false;
        }

        return true;
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
