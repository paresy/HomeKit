<?php

declare(strict_types=1);

include_once __DIR__ . '/SymconGlobalStubs.php';
include_once __DIR__ . '/SymconKernelStubs.php';
include_once __DIR__ . '/SymconModuleStubs.php';

use PHPUnit\Framework\TestCase;

class HomeKitDiscoveryTest extends TestCase
{
    private $discoveryModuleID = '{69D234C2-A453-4399-B766-71FB7D663700}';

    public function setUp()
    {
        //Register our i/o stubs for testing
        IPS\Kernel::loadLibrary(__DIR__ . '/SymconIOStubs/library.json');

        //Register our library we need for testing
        IPS\Kernel::loadLibrary(__DIR__ . '/../library.json');

        parent::setUp();
    }

    public function testCreate(): void
    {
        $iid = IPS_CreateInstance($this->discoveryModuleID);
        $this->assertEquals(sizeof(IPS_GetInstanceListByModuleID($this->discoveryModuleID)), 1);


    }

    public function testConfigurationForm(): void {
        $iid = IPS_CreateInstance($this->discoveryModuleID);
        $form = json_decode(IPS_GetConfigurationForParent($iid), true);

        $this->assertEquals($form, [
            'Host'               => '224.0.0.251',
            'Port'               => 5353,
            'BindPort'           => 5353,
            'MulticastIP'        => '224.0.0.251',
            'EnableBroadcast'    => true,
            'EnableReuseAddress' => true,
            'EnableLoopback'     => true
        ]);
    }
}
