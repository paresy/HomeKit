<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitLightbulbColorTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetVariableCustomProfile($vid, '~HexColor');
        IPS_SetVariableCustomAction($vid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 1,
                'Type'       => 'LightbulbColor',
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/LightbulbColor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 1,
                'Type'       => 'LightbulbColor',
                'Name'       => 'Test',
                'VariableID' => $vid /* The action/profile is missing */
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    //HomeKit sends On together with a new brightness. Only the final brightness has to be sent
    public function testSeparateBrightnessIsSentOnce(): void
    {
        $recorderID = $this->createRecorderScript();
        list($bridgeID, $colorID, $brightnessID) = $this->createBridgeWithColorLightbulb($recorderID, true);
        $manager = $this->getBridgeManager($bridgeID);

        //Switched off: On and Brightness arrive in one request
        SetValue($brightnessID, 0);
        $manager->beginAccessoryCache();
        $manager->writeCharacteristics(2, 102 /* On */, true);
        $manager->writeCharacteristics(2, 103 /* Brightness */, 40);
        $manager->endAccessoryCache();
        $this->assertEquals([$brightnessID => [40]], $GLOBALS['recorded']);

        //Switched on: the brightness is kept
        SetValue($brightnessID, 40);
        $GLOBALS['recorded'] = [];
        $manager->writeCharacteristics(2, 102 /* On */, true);
        $this->assertEquals([], $GLOBALS['recorded']);

        $manager->writeCharacteristics(2, 102 /* On */, false);
        $this->assertEquals([$brightnessID => [0]], $GLOBALS['recorded']);
    }

    public function testOnKeepsBrightnessOfColor(): void
    {
        $recorderID = $this->createRecorderScript();
        list($bridgeID, $colorID) = $this->createBridgeWithColorLightbulb($recorderID, false);
        $manager = $this->getBridgeManager($bridgeID);

        //Red with 50 % brightness stays as it is
        SetValue($colorID, 0x800000);
        $manager->writeCharacteristics(2, 102 /* On */, true);
        $this->assertEquals([], $GLOBALS['recorded']);

        $manager->writeCharacteristics(2, 102 /* On */, false);
        $this->assertEquals([$colorID => [0x000000]], $GLOBALS['recorded']);

        //Switched off: On and Brightness arrive in one request
        SetValue($colorID, 0x000000);
        $GLOBALS['recorded'] = [];
        $manager->beginAccessoryCache();
        $manager->writeCharacteristics(2, 102 /* On */, true);
        $manager->writeCharacteristics(2, 103 /* Brightness */, 40);
        $manager->endAccessoryCache();
        $this->assertEquals([$colorID => [0x666666]], $GLOBALS['recorded']);
    }

    private function createBridgeWithColorLightbulb(int $actionID, bool $separateBrightness): array
    {
        $bridgeID = $this->createBridge();

        $colorID = IPS_CreateVariable(1 /* Integer */);
        IPS_SetVariableCustomProfile($colorID, '~HexColor');
        IPS_SetVariableCustomAction($colorID, $actionID);

        $brightnessID = 0;
        if ($separateBrightness) {
            if (!IPS_VariableProfileExists('~Intensity.100')) {
                IPS_CreateVariableProfile('~Intensity.100', 1 /* Integer */);
                IPS_SetVariableProfileValues('~Intensity.100', 0, 100, 1);
            }
            $brightnessID = IPS_CreateVariable(1 /* Integer */);
            IPS_SetVariableCustomProfile($brightnessID, '~Intensity.100');
            IPS_SetVariableCustomAction($brightnessID, $actionID);
        }

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            ['ID' => 1, 'Type' => 'LightbulbColor', 'Name' => 'Test', 'VariableID' => $colorID, 'BrightnessID' => $brightnessID]
        ]));
        IPS_ApplyChanges($bridgeID);

        return [$bridgeID, $colorID, $brightnessID];
    }
}
