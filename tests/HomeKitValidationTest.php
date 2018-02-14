<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/Validator.php';

class HomeKitValidationTest extends TestCaseSymconValidation
{
    public function testValidateHomeKit(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateHomeKitBridge(): void
    {
        $this->validateModule(__DIR__ . '/../HomeKitBridge');
    }
}
