<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitDNSSDTest extends HomeKitBaseTest
{
    protected $dnssdModuleID = '{780B2D48-916C-4D59-AD35-5A429B2355A5}';

    private function cleanup()
    {
        $ids = IPS_GetInstanceListByModuleID($this->dnssdModuleID);
        foreach ($ids as $id) {
            IPS_DeleteInstance($id);
        }
    }

    public function testCreate(): void
    {
        $this->cleanup();

        IPS_CreateInstance($this->dnssdModuleID);
        $this->assertEquals(1, count(IPS_GetInstanceListByModuleID($this->dnssdModuleID)));
    }

    public function testBridgeServiceRegistration(): void
    {
        $this->cleanup();

        $dnssdID = IPS_CreateInstance($this->dnssdModuleID);
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        IPS_ApplyChanges($bridgeID);

        $expectedServiceProperty = [
            'Name'       => 'Symcon',
            'RegType'    => '_hap._tcp',
            'Domain'     => '',
            'Host'       => '',
            'Port'       => 34587,
            'TXTRecords' => [0 => ['Value' => 'md=Symcon'], 1 => ['Value' => 'pv=1.0'], 2 => ['Value' => 'id=3E:64:C3:71:BA:2B'], 3 => ['Value' => 'c#=1'], 4 => ['Value' => 's#=1'], 5 => ['Value' => 'ff=0'], 6 => ['Value' => 'ci=2'], 7 => ['Value' => 'sf=1']]
        ];

        $this->assertEquals([
            $expectedServiceProperty
        ], json_decode(IPS_GetProperty($dnssdID, 'Services'), true));

        //reset service
        IPS_SetProperty($dnssdID, 'Services', '[]');
        IPS_ApplyChanges($dnssdID);

        //simulate KR_READY event
        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $bridgeInterface->MessageSink(0, 0, IPS_KERNELMESSAGE, [KR_READY]);

        $this->assertEquals([
            $expectedServiceProperty
        ], json_decode(IPS_GetProperty($dnssdID, 'Services'), true));
    }
}
