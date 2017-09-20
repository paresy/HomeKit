<?php

declare(strict_types=1);

include_once __DIR__ . '/SymconValidator.php';

class SymconIOStubsValidationTest extends TestCaseSymconValidation
{
    public function testValidateIOStubs(): void
    {
        $this->validateLibrary(__DIR__ . '/SymconIOStubs');
    }

    public function testValidateClientSocket(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/ClientSocket');
    }

    public function testValidateMulticastSocket(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/MulticastSocket');
    }

    public function testValidateSerialPort(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/SerialPort');
    }

    public function testValidateServerSocket(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/ServerSocket');
    }

    public function testValidateUDPSocket(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/UDPSocket');
    }

    public function testValidateVirtualIO(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/VirtualIO');
    }

    public function testValidateWWWReader(): void
    {
        $this->validateModule(__DIR__ . '/SymconIOStubs/WWWReader');
    }
}
