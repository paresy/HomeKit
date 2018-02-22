<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitTemperatureSensorTest extends HomeKitBaseTest
{
    public function testAccessoryTemperatureSensor(): void
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

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $temperatureSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/TemperatureSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $temperatureSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryTemperatureSensorInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'AccessoryTemperatureSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $temperatureSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/TemperatureSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $temperatureSensor), $bridgeInterface->DebugAccessories());
    }
}
