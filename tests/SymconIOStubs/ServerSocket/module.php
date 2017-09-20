<?php

include_once __DIR__ . '/../VirtualIO/module.php';

class ServerSocket extends VirtualIO {

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString("Port", "");
        $this->RegisterPropertyInteger("Limit", 0);
    }

}