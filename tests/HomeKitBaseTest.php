<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/MessageStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';

use PHPUnit\Framework\TestCase;

class HomeKitBaseTest extends TestCase
{
    protected $bridgeModuleID = '{7FC71134-CFD0-4909-819C-B794FE067FBC}';
    protected $serverModuleID = '{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}';

    public function setUp(): void
    {
        //Reset
        IPS\Kernel::reset();

        //Register our i/o stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/IOStubs/library.json');

        //Register our core stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/CoreStubs/library.json');

        //Register our library we need for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/../library.json');

        parent::setUp();
    }

    public function testNop(): void
    {
        $this->assertTrue(true);
    }
}
