<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitTemperatureSensorTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        IPS_SetProperty($bridgeID, 'AccessoryTemperatureSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/TemperatureSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        SetValue($vid, -500);

        IPS_SetProperty($bridgeID, 'AccessoryTemperatureSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/TemperatureSensor.json'), true);

        $accessory[0]['services'][1]['characteristics'][0]['value'] = -100.0;

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }
}
