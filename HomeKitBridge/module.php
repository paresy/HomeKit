<?php

declare(strict_types=1);

include_once __DIR__ . '/../libs/DNSSDModule.php';
include_once __DIR__ . '/pairings.php';
include_once __DIR__ . '/codes.php';
include_once __DIR__ . '/manager.php';
include_once __DIR__ . '/session.php';
include_once __DIR__ . '/hap.php';
include_once __DIR__ . '/simulate.php';
include_once __DIR__ . '/helper/autoload.php';
include_once __DIR__ . '/characteristics/autoload.php';
include_once __DIR__ . '/services/autoload.php';
include_once __DIR__ . '/accessories/autoload.php';

class HomeKitBridge extends DNSSDModule
{
    private $pairings = null;
    private $codes = null;
    private $manager = null;

    use Simulate;

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID, '', '', '', '', 0, []);

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

        $this->RegisterPropertyString('BridgeID', implode(':', array_slice(str_split(strtoupper(md5(IPS_GetLicensee())), 2), 0, 6)));
        $this->RegisterPropertyString('BridgeName', 'Symcon');
        $this->RegisterPropertyInteger('BridgePort', 34587);

        $this->RegisterPropertyString('AccessoryKeyPair', bin2hex(sodium_crypto_sign_keypair()));
        $this->RegisterPropertyString('Pairings', '[]');

        //Always create our own ServerSocket, when no parent is already available
        $this->RequireParent('{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}');

        $this->RegisterTimer('Cleanup', 0, 'HK_Cleanup($_IPS["TARGET"]);');

        //Each accessory is allowed to register properties for persistent data
        $this->manager->registerProperties();
    }

    public function GetConfigurationForParent()
    {
        return json_encode([
            'Port' => $this->ReadPropertyInteger('BridgePort')
        ]);
    }

    public function GetConfigurationForm()
    {
        $pairing = [
            [
                'type'  => 'RowLayout',
                'items' => [
                    [
                        'type'    => 'Button',
                        'label'   => 'Request setup code!',
                        'onClick' => 'echo HK_RestartPairing($id);'
                    ],
                    [
                        'type'  => 'Label',
                        'label' => 'Press the button to start the pairing process. Code is valid for at most 5 minutes.'
                    ]
                ]
            ]
        ];

        $label = [
            [
                'type'  => 'Label',
                'label' => ''
            ],
            [
                'type'  => 'Label',
                'label' => 'You can add new items for each accessory type'
            ]
        ];

        $dnssd = [
            [
                'type'      => 'PopupButton',
                'caption'   => 'Expert options',
                'popup'     => [
                    'caption'   => 'Expert options',
                    'items'     => [
                        [
                            'type'  => 'Label',
                            'label' => 'These options are for experts only! Do not touch!'
                        ],
                        [
                            'type'  => 'Label',
                            'label' => 'After changing the name please delete the old entry in the DNS-SD control'
                        ],
                        [
                            'type'    => 'ValidationTextBox',
                            'caption' => 'Name',
                            'name'    => 'BridgeName'
                        ],
                        [
                            'type'    => 'ValidationTextBox',
                            'caption' => 'ID',
                            'name'    => 'BridgeID'
                        ],
                        [
                            'type'    => 'NumberSpinner',
                            'caption' => 'Port',
                            'name'    => 'BridgePort'
                        ]
                    ]
                ]
            ]
        ];

        $translations = [
            'de' => [
                'Name'                                                                                => 'Name',
                'ID'                                                                                  => 'ID',
                'Port'                                                                                => 'Port',
                'Name is required to consist only of letters and numbers!'                            => 'Name darf nur aus Buchstaben und Zahlen bestehen!',
                'ID is not a valid MAC style address!'                                                => 'ID muss wie eine MAC Adresse aussehen!',
                'OK!'                                                                                 => 'OK!',
                'Press the button to start the pairing process. Code is valid for at most 5 minutes.' => 'Hier drücken, um das Verknüpfen zu Starten. Der Code ist für maximal 5 Minuten gültig.',
                'Request setup code!'                                                                 => 'Code anfordern!',
                'You can add new items for each accessory type'                                       => 'Pro Gerätetyp können Einträge hinzugefügt werden',
                'These options are for experts only! Do not touch!'                                   => 'Diese Einstellungen sind nur für Experten. Nicht anfassen!',
                'Expert options'                                                                      => 'Expertenoptionen',
                'After changing the name please delete the old entry in the DNS-SD control'           => 'Nach einer Namensänderung muss der alte Eintrag im DNS-SD Control manuell gelöscht werden',
                'Our parent instance (ServerSocket) is not active!'                                   => 'Die übergeordnete Instanz (ServerSocket) ist nicht aktiv!'
            ]
        ];

        $formFront = [
            'elements'     => array_merge($pairing, $label),
            'translations' => $translations
        ];

        $formBack = [
            'elements' => $dnssd
        ];

        return json_encode(array_merge_recursive($formFront, $this->manager->getConfigurationForm(), $formBack));
    }

    public function ForwardData($JSONString)
    {
        return '';
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);

        //Decode buffer
        $buffer = utf8_decode($data->Buffer);

        //Show some debug data
        switch ($data->Type) {
            case 0: /* Data */
                $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Received: ' . $buffer, 0);
                break;
            case 1: /* Connected */
                $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Connected', 0);
                return;
            case 2: /* Disconnected */
                $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Disconnected', 0);
                $this->clearSession($data->ClientIP, $data->ClientPort);
                return;
        }

        //Get Session for ClientIP/ClientPort
        $session = $this->getSession($data->ClientIP, $data->ClientPort);

        //Add new data and process it inside the session
        $response = $session->processData($buffer);

        //Only if we have a valid response
        if ($response !== null) {
            $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Transmit: ' . $response, 0);

            //Send response
            $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => utf8_encode($response), 'ClientIP' => $data->ClientIP, 'ClientPort' => $data->ClientPort]));
        }

        //Save session for ClientIP/ClientPort
        $this->setSession($data->ClientIP, $data->ClientPort, $session);
    }

    public function Cleanup()
    {

        //This function is used to properly disconnect sessions after any responses were send
        foreach ($this->GetBufferList() as $name) {
            $json = json_decode($this->GetBuffer($name));
            if (isset($json->locked) && $json->locked) {
                list($clientIP, $clientPort) = explode(':', $name);

                //Send disconnect request
                $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Requesting disconnect...', 0);
                $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => '', 'ClientIP' => $clientIP, 'ClientPort' => intval($clientPort), 'Type' => 2 /* Disconnect */]));

                //Remove session
                $this->clearSession($clientIP, intval($clientPort));
            }
        }

        //Deactivate cleanup timer
        $this->SetTimerInterval('Cleanup', 0);
    }

    private function UpdateDNSSD()
    {

        // Verify name compliance
        if (ctype_alnum($this->ReadPropertyString('BridgeName')) === false) {
            echo $this->Translate('Name is required to consist only of letters and numbers!');
        }

        // Verify id compliance
        if (filter_var($this->ReadPropertyString('BridgeID'), FILTER_VALIDATE_MAC) === false) {
            echo $this->Translate('ID is not a valid MAC style address!');
        }

        // Update DNSSD Service parameters before we call ApplyChanges, which will update DNSSD the service
        $this->UpdateService(
            $this->ReadPropertyString('BridgeName'),
            '_hap._tcp',
            '',
            '',
            $this->ReadPropertyInteger('BridgePort'),
            [
                'md=' . $this->ReadPropertyString('BridgeName'),
                'pv=1.0',
                'id=' . $this->ReadPropertyString('BridgeID'),
                'c#=' . $this->ReadPropertyString('ConfigurationNumber'), /* This is registered inside manager.php */
                's#=1',
                'ff=0', /* Switch to 1 when we have a MFi certificate */
                'ci=2',
                'sf=' . ($this->pairings->hasPairings() ? '0' : '1') /* Do not allow more than one pairing */
            ]
        );
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        //Update DNSSD parameters before we register the service
        $this->UpdateDNSSD();

        //Forward variable events to sessions
        if (($Message == VM_UPDATE) && $Data[1] /* Changed */) {
            $this->processNotifications($SenderID, $Data[0] /* Value */);
        }

        // Diese Zeile nicht löschen
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
    }

    public function ApplyChanges()
    {

        //Update DNSSD parameters before we register the service
        $this->UpdateDNSSD();

        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        // We need to check for IDs that have the value zero and assign a proper ID
        if ($this->manager->updateAccessories()) {

            // If we had changes we need to clear subscriptions which might be invalid.
            $this->clearSessionSubscriptions();
        }
    }

    public function RestartPairing()
    {

        //Check if our parent instance (ServerSocket) is active
        $pid = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if (IPS_GetInstance($pid)['InstanceStatus'] != 102 /* IS_ACTIVE */) {
            echo $this->Translate('Our parent instance (ServerSocket) is not active!');

            return '';
        }

        //Remove all pairings before staring the pairing process
        $this->pairings->clearPairings();

        //Update DNSSD to announce we are fresh and ready to go
        $this->UpdateDNSSD();

        //Only generate new setup code when required
        $setupCode = $this->codes->getSetupCode();
        if (!$setupCode) {
            $this->SendDebug('HomeKitPairing', 'Creating new setup code for pairing process!', 0);
            $setupCode = $this->codes->generateSetupCode();
        }

        return $setupCode;
    }

    private function clearSession(string $clientIP, int $clientPort)
    {
        $this->SetBuffer($clientIP . ':' . $clientPort, '');
    }

    private function getSession(string $clientIP, int $clientPort)
    {
        $data = $this->GetBuffer($clientIP . ':' . $clientPort);

        $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Read Session: ' . $data, 0);

        return new HomeKitSession(
            function ($Message, $Data, $Type) {
                $this->SendDebug($Message, $Data, $Type);
            },
            function ($VariableID) {
                $this->RegisterMessage($VariableID, VM_UPDATE);
            },
            $this->pairings,
            $this->codes,
            $this->manager,
            $this->ReadPropertyString('BridgeID'),
            hex2bin($this->ReadPropertyString('AccessoryKeyPair')),
            $data,
            function ($Identifier) {
                $this->terminateSessions($Identifier);
            }
        );
    }

    private function setSession(string $clientIP, int $clientPort, HomeKitSession $session)
    {
        $data = $session->__toString();

        $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Write Session: ' . $data, 0);

        if (!($session instanceof HomeKitSession)) {
            throw new Exception('HomeKitSession expected as parameter type!');
        }

        $this->SetBuffer($clientIP . ':' . $clientPort, $data);
    }

    private function terminateSessions($Identifier)
    {
        $this->SendDebug('HomeKit', 'Terminate Sessions: ' . $Identifier, 0);

        foreach ($this->GetBufferList() as $name) {
            $json = json_decode($this->GetBuffer($name));
            if (isset($json->identifier) && ($json->identifier == $Identifier)) {
                //this will lock the session for further communication
                $json->locked = true;
                $this->SetBuffer($name, json_encode($json));
            }
        }

        //Activate cleanup timer
        $this->SetTimerInterval('Cleanup', 3 * 1000);
    }

    private function processNotifications($VariableID, $Value)
    {
        $this->SendDebug('Notify Event', 'VariableID ' . $VariableID . ' = ' . var_export($Value, true), 0);
        foreach ($this->GetBufferList() as $name) {
            //check for a colon, which indicates an ip / port combination
            //filter different buffers we use like e.g. SetupCode
            if (strpos($name, ':') !== false) {
                list($clientIP, $clientPort) = explode(':', $name);

                //Get Session for ClientIP/ClientPort
                $session = $this->getSession($clientIP, intval($clientPort));

                //Check for valid events and build response
                $response = $session->notifyVariable($VariableID, $Value);

                //Only if we have a valid response
                if ($response !== null) {
                    $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Transmit: ' . $response, 0);

                    //Send response
                    $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => utf8_encode($response), 'ClientIP' => $clientIP, 'ClientPort' => intval($clientPort)]));
                }

                //Save session for ClientIP/ClientPort
                $this->setSession($clientIP, intval($clientPort), $session);
            }
        }
    }

    private function clearSessionSubscriptions()
    {
        foreach ($this->GetBufferList() as $name) {
            //check for a colon, which indicates an ip / port combination
            //filter different buffers we use like e.g. SetupCode
            if (strpos($name, ':') !== false) {
                list($clientIP, $clientPort) = explode(':', $name);

                //Get Session for ClientIP/ClientPort
                $session = $this->getSession($clientIP, intval($clientPort));

                //Just clear it. Client will resubscribe
                $session->clearSubscriptions();

                //Save session for ClientIP/ClientPort
                $this->setSession($clientIP, intval($clientPort), $session);
            }
        }
    }
}
