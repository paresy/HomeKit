<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitLightbulbExpertTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $sid = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($sid, 10001); //Any valid ID will do

        $hid = IPS_CreateVariable(1 /* Integer */);

        //Currently stubs do not provide default profiles
        if (!IPS_VariableProfileExists('~Intensity.100')) {
            IPS_CreateVariableProfile('~Intensity.100', 1 /* Integer */);
            IPS_SetVariableProfileValues('~Intensity.100', 0, 100, 1);
        }

        IPS_SetVariableCustomProfile($hid, '~Intensity.100'); //Any valid profile will do
        IPS_SetVariableCustomAction($hid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'           => 1,
                'Type'         => 'LightbulbExpert',
                'Name'         => 'Test',
                'StateID'      => $sid,
                'BrightnessID' => $hid,
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/LightbulbExpert.json'), true);

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
                'Type'       => 'LightbulbDimmer',
                'Name'       => 'Test',
                'VariableID' => $vid /* The profile/action is missing */
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    //The brightness variable has to be dimmable as well
    public function testBrokenBrightnessVariable(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $sid = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($sid, 10001); //Any valid ID will do

        $hid = IPS_CreateVariable(1 /* Integer */); //Neither profile nor action

        $data = [
            'ID'           => 1,
            'Type'         => 'LightbulbExpert',
            'Name'         => 'Test',
            'StateID'      => $sid,
            'BrightnessID' => $hid,
        ];
        $this->assertNotEquals('OK', HAPAccessoryConfigurationLightbulbExpert::getStatus($data));

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([$data]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), $bridgeInterface->DebugAccessories());
    }
}
