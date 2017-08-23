<?php

declare(strict_types=1);

include_once __DIR__ . '/SymconValidator.php';

class HomeKitTest extends TestCaseSymconValidation
{
    public function testValidateHomeKit(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateHomeKitDiscovery(): void
    {
        $this->validateModule(__DIR__ . '/../HomeKitDiscovery');
    }

    public function testValidateHomeKitBridge(): void
    {
        $this->validateModule(__DIR__ . '/../HomeKitBridge');
    }
}
