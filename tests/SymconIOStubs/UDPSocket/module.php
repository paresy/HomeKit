<?php

declare(strict_types=1);
include_once __DIR__ . '/../VirtualIO/module.php';

class UDPSocket extends VirtualIO
{
    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyInteger('Port', 0);
        $this->RegisterPropertyString('BindIP', '');
        $this->RegisterPropertyInteger('BindPort', 0);
    }
}
