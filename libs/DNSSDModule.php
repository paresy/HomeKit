<?php

declare(strict_types=1);

class DNSSDModule extends IPSModule
{
    private $name = '';
    private $regType = '';
    private $domain = '';
    private $host = '';
    private $port = 0;
    private $txtRecords = [];

    public function __construct(int $InstanceID, string $Name, string $RegType, string $Domain, string $Host, int $Port, array $TXTRecords)
    {
        parent::__construct($InstanceID);

        $this->UpdateService($Name, $RegType, $Domain, $Host, $Port, $TXTRecords);
    }

    protected function UpdateService(string $Name, string $RegType, string $Domain, string $Host, int $Port, array $TXTRecords)
    {
        $this->name = $Name;
        $this->regType = $RegType;
        $this->domain = $Domain;
        $this->host = $Host;
        $this->port = $Port;
        $this->txtRecords = $TXTRecords;
    }

    public function Create()
    {

        //Never delete this line!
        parent::Create();

        //We need to call the RegisterHook function on Kernel READY
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        //Never delete this line!
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if ($Message == IPS_KERNELMESSAGE && $Data[0] == KR_READY) {
            $this->RegisterService($this->name, $this->regType, $this->domain, $this->host, $this->port, $this->txtRecords);
        }
    }

    public function ApplyChanges()
    {

        //Never delete this line!
        parent::ApplyChanges();

        //Only call this in READY state. On startup the DNSSD instance might not be available yet
        if (IPS_GetKernelRunlevel() == KR_READY) {
            $this->RegisterService($this->name, $this->regType, $this->domain, $this->host, $this->port, $this->txtRecords);
        }
    }

    private function RegisterService(string $Name, string $RegType, string $Domain, string $Host, int $Port, array $TXTRecords)
    {
        $ids = IPS_GetInstanceListByModuleID('{780B2D48-916C-4D59-AD35-5A429B2355A5}');
        if (!empty($ids)) {
            $services = json_decode(IPS_GetProperty($ids[0], 'Services'), true);
            $found = false;
            $changes = false;

            $expandedRecords = $this->ExpandRecords($TXTRecords);
            foreach ($services as $index => $service) {
                if ($service['Name'] == $Name) {
                    if (!empty($RegType) && $service['RegType'] != $RegType) {
                        $services[$index]['RegType'] = $RegType;
                        $changes = true;
                    }

                    if (!empty($Domain) && $service['Domain'] != $Domain) {
                        $services[$index]['Domain'] = $Domain;
                        $changes = true;
                    }

                    if (!empty($Host) && $service['Host'] != $Host) {
                        $services[$index]['Host'] = $Host;
                        $changes = true;
                    }

                    if (!empty($Port) && $service['Port'] != $Port) {
                        $services[$index]['Port'] = $Port;
                        $changes = true;
                    }

                    if (!empty($expandedRecords) && $service['TXTRecords'] != $expandedRecords) {
                        $services[$index]['TXTRecords'] = $expandedRecords;
                        $changes = true;
                    }

                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $services[] = [
                    'Name'       => $Name,
                    'RegType'    => $RegType,
                    'Domain'     => $Domain,
                    'Host'       => $Host,
                    'Port'       => $Port,
                    'TXTRecords' => $expandedRecords
                ];
                $changes = true;
            }

            if ($changes) {
                IPS_SetProperty($ids[0], 'Services', json_encode($services));
                IPS_ApplyChanges($ids[0]);
            }
        }
    }

    private function ExpandRecords(array $TXTRecords)
    {
        //Lets expand the array our more complicated persistence format

        $result = [];
        foreach ($TXTRecords as $TXTRecord) {
            $result[] = ['Value' => $TXTRecord];
        }
        return $result;
    }
}
