<?php

declare(strict_types=1);

include_once __DIR__ . '/SymconGlobalStubs.php';
include_once __DIR__ . '/SymconKernelStubs.php';
include_once __DIR__ . '/SymconModuleStubs.php';
include_once __DIR__ . '/../HomeKitDiscovery/module.php';

use PHPUnit\Framework\TestCase;

class HomeKitDiscoveryTest extends TestCase
{
    public function setUp()
    {
        //Register our library we need for testing
        IPS\Kernel::loadLibrary(__DIR__ . '/../library.json');

        parent::setUp();
    }

    public function testCreate(): void
    {
        $instance = new HomeKitDiscovery(0);
        $instance->Create();
        $form = json_decode($instance->GetConfigurationForParent(), true);

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
