<?php

include_once __DIR__ . '/../UDPSocket/module.php';

class MulticastSocket extends UDPSocket {

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString("MulticastIP", "");
        $this->RegisterPropertyBoolean("EnableBroadcast", false);
        $this->RegisterPropertyBoolean("EnableReuseAddress", false);
        $this->RegisterPropertyBoolean("EnableLoopback", false);
    }

}