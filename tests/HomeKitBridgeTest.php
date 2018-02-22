<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitBridgeTest extends HomeKitBaseTest
{
    public function testCreate(): void
    {
        $iid = IPS_CreateInstance($this->bridgeModuleID);
        $this->assertEquals(1, count(IPS_GetInstanceListByModuleID($this->bridgeModuleID)));
        $this->assertEquals(1, count(IPS_GetInstanceListByModuleID($this->serverModuleID)));
        $this->assertEquals(IPS_GetInstanceListByModuleID($this->serverModuleID)[0], IPS_GetInstance($iid)['ConnectionID']);
    }

    public function testConfigurationForm(): void
    {
        $iid = IPS_CreateInstance($this->bridgeModuleID);
        $form = json_decode(IPS_GetConfigurationForParent($iid), true);

        $this->assertEquals([
            'Port' => IPS_GetProperty($iid, 'BridgePort')
        ], $form);
    }

    public function testAccessories(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true), $bridgeInterface->DebugAccessories());
    }

}
