<?php

include_once __DIR__ . '/../libs/vendor/autoload.php';
include_once 'pairings.php';
include_once 'codes.php';
include_once 'manager.php';
include_once 'session.php';
include_once 'hap.php';
include_once 'characteristics/autoload.php';
include_once 'services/autoload.php';
include_once 'accessories/autoload.php';

class HomeKitBridge extends IPSModule
{
    private $pairings = null;
    private $codes = null;
    private $manager = null;

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID);

        //Prepare a few basics
        $this->pairings = new HomeKitPairings(
            $this->InstanceID,
            function ($Message, $Data, $Type) {
                $this->SendDebug($Message, $Data, $Type);
            }
        );
        $this->codes = new HomeKitCodes(
            $this->InstanceID,
            function ($Message, $Data, $Type) {
                $this->SendDebug($Message, $Data, $Type);
            },
            function ($Name) {
                return $this->GetBuffer($Name);
            },
            function ($Name, $Value) {
                $this->SetBuffer($Name, $Value);
            }
        );
        $this->manager = new HomeKitManager(
            $this->InstanceID,
            function ($Name, $Value) {
                $this->RegisterPropertyString($Name, $Value);
            }
        );
    }

    public function Create()
    {

        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyInteger('DiscoveryInstanceID', 0);
        $this->RegisterPropertyString('AccessoryKeyPair', bin2hex(sodium_crypto_sign_keypair()));
        $this->RegisterPropertyString('Pairings', '[]');

        //Always create our own ServerSocket, when no parent is already available
        $this->RequireParent('{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}');

        //Each accessory is allowed to register properties for persistent data
        $this->manager->registerProperties();
    }

    public function GetConfigurationForParent()
    {
        if ($this->isDiscoveryInstanceValid()) {
            return json_encode([
                'Port' => IPS_GetProperty($this->ReadPropertyInteger('DiscoveryInstanceID'), 'BridgePort')
            ]);
        } else {
            return json_encode(
                [
                    'Open' => false,
                    'Port' => 0
                ]
            );
        }
    }

    public function GetConfigurationForm()
    {
        $discovery = [];

        //Check if we already have assigned a valid HomeKit discovery instance. Otherwise show a button to create it
        if (!$this->isDiscoveryInstanceValid()) {
            $discovery = [
                [
                    'type'  => 'Label',
                    'label' => 'Before adding the bridge to your iOS device, please create the HomeKit Discovery service.'
                ],
                [
                    'type'    => 'Button',
                    'label'   => 'Create Discovery service',
                    'onClick' => <<<'EOT'
						if(sizeof(IPS_GetInstanceListByModuleID("{69D234C2-A453-4399-B766-71FB7D663700}")) > 0) { 
							echo (new IPSModule($id))->Translate("You already have created a HomeKit discovery service!"); 
						} else {
						 	//Create Discovery instance
							$iid = IPS_CreateInstance("{69D234C2-A453-4399-B766-71FB7D663700}");
							IPS_SetName($iid, (new IPSModule($id))->Translate("HomeKit Discovery"));
							$pid = IPS_GetInstance($iid)['ConnectionID'];
							$configuration = json_decode(IPS_GetConfigurationForParent($iid));
							$configuration->Open = true;
							IPS_SetConfiguration($pid, json_encode($configuration));
							IPS_ApplyChanges($pid);
							//Self reconfigure ourselves to assign the new Discovery instance
							IPS_SetProperty($id, "DiscoveryInstanceID", $iid);
							IPS_ApplyChanges($id);
							echo (new IPSModule($id))->Translate("OK!");
						}
EOT
                ]
            ];
        }

        $pairing = [
            [
                'type'  => 'Label',
                'label' => 'Press the button to generate a setup code. It is valid for one pairing and at most 5 minutes.'
            ],
            [
                'type'    => 'Button',
                'label'   => 'Request setup code!',
                'onClick' => 'echo HK_GenerateSetupCode($id);'
            ],
            [
                'type'  => 'Label',
                'label' => 'You can add new items for each accessory type:'
            ]
        ];

        $discoveryLink = [
            [
                'type'  => 'Label',
                'label' => 'Experts only! Do not change!'
            ],
            [
                'type'    => 'SelectInstance',
                'caption' => 'Discovery Instance',
                'name'    => 'DiscoveryInstanceID'
            ]
        ];

        $accessories = $this->manager->getConfigurationForm();

        return json_encode(['elements' => array_merge($discovery, $pairing, $accessories, $discoveryLink)]);
    }

    public function ForwardData($JSONString)
    {
        return '';
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);

        //Check Discovery service
        if (!$this->isDiscoveryInstanceValid()) {
            $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Discovery Service is missing!', 0);

            return;
        }

        //Decode buffer
        $buffer = utf8_decode($data->Buffer);

        //Show some debug data
        $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Received: ' . $buffer, 0);

        //Get Session for ClientIP/ClientPort
        $session = $this->getSession($data->ClientIP, $data->ClientPort);

        //Add new data and process it inside the session
        $response = $session->processData($buffer);

        $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Transmit: ' . $response, 0);

        //Send response
        if ($response != null) {
            $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => utf8_encode($response), 'ClientIP' => $data->ClientIP, 'ClientPort' => $data->ClientPort]));
        }

        //Save session for ClientIP/ClientPort
        $this->setSession($data->ClientIP, $data->ClientPort, $session);
    }

    public function ApplyChanges()
    {

        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        // Verify that our Discovery instanceID is valid
        if ($this->ReadPropertyInteger('DiscoveryInstanceID') > 0) {
            if (!$this->isDiscoveryInstanceValid()) {
                echo $this->Translate('Selected InstanceID is not a valid HomeKit Discovery instance!');
            }
        }

        // We need to check for IDs that have the value zero and assign a proper ID
        $this->manager->updateAccessories();
    }

    public function GenerateSetupCode()
    {

        //Check if the HomeKit Discovery service is created
        if (!$this->isDiscoveryInstanceValid()) {
            echo $this->Translate('You need the HomeKit Discovery service before generating a setup code!');

            return;
        }

        //Check if the HomeKit Discovery service is active
        $pid = IPS_GetInstance($this->ReadPropertyInteger('DiscoveryInstanceID'))['ConnectionID'];
        if (IPS_GetInstance($pid)['InstanceStatus'] != 102 /* IS_ACTIVE */) {
            echo $this->Translate('The HomeKit Discovery service is not active!');

            return;
        }

        //Check if our parent instance (ServerSocket) is active
        $pid = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if (IPS_GetInstance($pid)['InstanceStatus'] != 102 /* IS_ACTIVE */) {
            echo $this->Translate('Our parent instance (ServerSocket) is not active!');

            return;
        }

        echo $this->codes->generateSetupCode();
    }

    private function isDiscoveryInstanceValid()
    {
        $announceInstanceID = $this->ReadPropertyInteger('DiscoveryInstanceID');
        if ($announceInstanceID > 0) {
            if (IPS_InstanceExists($announceInstanceID)) {
                $i = IPS_GetInstance($announceInstanceID);
                if ($i['ModuleInfo']['ModuleID'] == '{69D234C2-A453-4399-B766-71FB7D663700}') {
                    return true;
                }
            }
        }

        return false;
    }

    private function getSession($clientIP, $clientPort)
    {
        $data = $this->GetBuffer($clientIP . ':' . $clientPort);

        $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Read Session: ' . $data, 0);

        return new HomeKitSession(
            function ($Message, $Data, $Type) {
                $this->SendDebug($Message, $Data, $Type);
            },
            $this->pairings,
            $this->codes,
            $this->manager,
            IPS_GetProperty($this->ReadPropertyInteger('DiscoveryInstanceID'), 'BridgeID'),
            hex2bin($this->ReadPropertyString('AccessoryKeyPair')),
            $data
        );
    }

    private function setSession($clientIP, $clientPort, $session)
    {
        $data = $session->__toString();

        $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Write Session: ' . $data, 0);

        if (!($session instanceof HomeKitSession)) {
            throw new Exception('HomeKitSession expected as parameter type!');
        }

        $this->SetBuffer($clientIP . ':' . $clientPort, $data);
    }

    //TODO: Remove at some point...
    public function DebugAccessories()
    {
        echo json_encode($this->manager->getAccessories(), JSON_PRETTY_PRINT);
    }
}
