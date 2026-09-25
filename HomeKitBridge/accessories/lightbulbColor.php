<?php

declare(strict_types=1);

include_once __DIR__ . '/lightbulbSwitch.php';

class HAPAccessoryLightbulbColor extends HAPAccessoryLightbulbSwitch
{
    use HelperColorDevice;
    use HelperDimDevice;

    //HomeKit sends Hue, Saturation and Brightness as separate writes within one request. Later writes need to
    //compose against the pending value instead of GetValue, which is not updated yet after the detached send
    private $pendingRGB = null;

    public function notifyCharacteristicOn()
    {
        return [
            $this->getBrightnessVariableID()
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
            $this->getBrightnessVariableID()
        ];
    }

    public function readCharacteristicBrightness()
    {
        if ($this->hasSeparateBrightness()) {
            return self::getDimValue($this->data['BrightnessID']);
        }

        $hsb = self::rgbToHSB(self::getColorValue($this->data['VariableID']));
        return $hsb['brightness'] * 100;
    }

    public function writeCharacteristicBrightness($value)
    {
        if ($this->hasSeparateBrightness()) {
            self::dimDevice($this->data['BrightnessID'], $value);
            return;
        }

        $this->writeComposedHSB('brightness', $value / 100);
    }

    public function notifyCharacteristicHue()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicHue()
    {
        $hsb = self::rgbToHSB(self::getColorValue($this->data['VariableID']));
        return $hsb['hue'];
    }

    public function writeCharacteristicHue($value)
    {
        $this->writeComposedHSB('hue', $value);
    }

    public function notifyCharacteristicSaturation()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicSaturation()
    {
        $hsb = self::rgbToHSB(self::getColorValue($this->data['VariableID']));
        return $hsb['saturation'] * 100;
    }

    public function writeCharacteristicSaturation($value)
    {
        $this->writeComposedHSB('saturation', $value / 100);
    }

    public function flushWriteCharacteristics(): void
    {
        if ($this->pendingRGB !== null) {
            self::colorDevice($this->data['VariableID'], $this->pendingRGB);
            $this->pendingRGB = null;
        }
    }

    private function hasSeparateBrightness()
    {
        return IPS_VariableExists($this->data['BrightnessID'] ?? 0);
    }

    private function getBrightnessVariableID()
    {
        if ($this->hasSeparateBrightness()) {
            return $this->data['BrightnessID'];
        }

        return $this->data['VariableID'];
    }

    private function writeComposedHSB($component, $value)
    {
        $hsb = self::rgbToHSB($this->pendingRGB ?? self::getColorValue($this->data['VariableID']));
        $hsb[$component] = $value;
        $this->pendingRGB = self::hsbToRGB($hsb['hue'], $hsb['saturation'], $hsb['brightness']);
    }
}

class HAPAccessoryConfigurationLightbulbColor extends HAPAccessoryConfigurationLightbulbSwitch
{
    use HelperColorDevice;
    use HelperDimDevice;

    public static function getPosition()
    {
        return 3;
    }

    public static function getCaption()
    {
        return 'Lightbulb (Color)';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
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

    public static function doMigrate(&$data)
    {
        //The separate brightness variable is optional and was added later
        if (!isset($data['BrightnessID'])) {
            $data['BrightnessID'] = 0;
            return true;
        }
        return false;
    }

    public static function getObjectIDs($data)
    {
        $ids = [
            $data['VariableID'],
        ];
        if (IPS_VariableExists($data['BrightnessID'] ?? 0)) {
            $ids[] = $data['BrightnessID'];
        }
        return $ids;
    }

    public static function getStatus($data)
    {
        $hasSeparateBrightness = IPS_VariableExists($data['BrightnessID'] ?? 0);

        if ($hasSeparateBrightness) {
            $status = self::getDimCompatibility($data['BrightnessID']);
            if ($status != 'OK') {
                return $status;
            }
        }

        return self::getColorCompatibility($data['VariableID'], $hasSeparateBrightness);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Lightbulb (Color)'                          => 'Lampe (Farbig)',
                'VariableID'                                 => 'VariablenID',
                'BrightnessID'                               => 'HelligkeitsID',
                'Variable missing'                           => 'Variable fehlt',
                'Int required'                               => 'Int benötigt',
                'Int/Float required'                         => 'Int/Float benötigt',
                'Profile required'                           => 'Profil benötigt',
                'HexColor required'                          => 'HexColor benötigt',
                'Action required'                            => 'Aktion benötigt',
                'Separate brightness required for xy encoding' => 'Separate Helligkeit für xy-Kodierung benötigt',
                'OK'                                         => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbColor');
