<?php

declare(strict_types=1);
include_once __DIR__ . '/../VirtualIO/module.php';

class SerialPort extends VirtualIO
{
    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString('Port', '');
        $this->RegisterPropertyString('BaudRate', '9600');
        $this->RegisterPropertyString('DataBits', '8');
        $this->RegisterPropertyString('StopBits', '1');
        $this->RegisterPropertyString('Parity', 'None');
    }
}
