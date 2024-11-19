<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitLockTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(0 /* Boolean */);

        IPS_SetVariableCustomAction($vid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'AccessoryLock', json_encode([
            [
                'ID'                    => 3,
                'Name'                  => 'Test',
                'VariableID'            => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/Lock.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }
}
