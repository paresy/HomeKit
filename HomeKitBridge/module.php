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
    use Simulate;
    private $pairings = null;
    private $codes = null;
    private $manager = null;

    public function __construct(int $InstanceID)
    {
        parent::__construct($InstanceID, '', '', '', '', 0, []);

        //Prepare a few basics
        $this->pairings = new HomeKitPairings(
            $this->InstanceID,
            function ($Message, $Data, $Type)
            {
                $this->SendDebug($Message, $Data, $Type);
            }
        );
        $this->codes = new HomeKitCodes(
            $this->InstanceID,
            function ($Message, $Data, $Type)
            {
                $this->SendDebug($Message, $Data, $Type);
            },
            function ($Name)
            {
                return $this->GetBuffer($Name);
            },
            function ($Name, $Value)
            {
                $this->SetBuffer($Name, $Value);
            }
        );
        $this->manager = new HomeKitManager(
            $this->InstanceID,
            function ($Name, $Value)
            {
                $this->RegisterPropertyString($Name, $Value);
            },
            function ($ID)
            {
                $this->RegisterReference($ID);
            },
            function ($Export)
            {
                $this->onAccessoriesExported($Export);
            }
        );
    }

    public function Create(): void
    {

        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString('BridgeID', implode(':', array_slice(str_split(strtoupper(md5(IPS_GetLicensee())), 2), 0, 6)));
        $this->RegisterPropertyString('BridgeName', 'Symcon');
        $this->RegisterPropertyInteger('BridgePort', 34587);

        $this->RegisterPropertyString('AccessoryKeyPair', bin2hex(sodium_crypto_sign_keypair()));
        $this->RegisterPropertyString('Pairings', '[]');

        //Advertised as s#. Incremented when the state changes while no controller is connected
        $this->RegisterAttributeInteger('CurrentStateNumber', 1);

        //Multiple state changes only increment the state number once, until a controller connects again
        $this->RegisterAttributeBoolean('StateNumberPending', false);

        //Incremented whenever the exported accessories change, so controllers reload them
        $this->RegisterAttributeInteger('ConfigurationNumber', 0);
        $this->RegisterAttributeString('ConfigurationHash', '');

        //Links the setup QR code to this bridge via the sh TXT record
        $this->RegisterAttributeString('SetupID', '');

        $this->RegisterTimer('Cleanup', 0, 'HK_Cleanup($_IPS["TARGET"]);');

        //Each accessory is allowed to register properties for persistent data
        $this->manager->registerProperties();

        //Create special profile for SecuritySystem
        if (!IPS_VariableProfileExists('SecuritySystem.HomeKit')) {
            // We need to add a static locale.json because $this->Translate does not load translations for $this->GetConfigurationForm
            IPS_CreateVariableProfile('SecuritySystem.HomeKit', VARIABLETYPE_INTEGER);
            IPS_SetVariableProfileAssociation('SecuritySystem.HomeKit', 0, $this->Translate('Stay'), '', -1);
            IPS_SetVariableProfileAssociation('SecuritySystem.HomeKit', 1, $this->Translate('Away'), '', -1);
            IPS_SetVariableProfileAssociation('SecuritySystem.HomeKit', 2, $this->Translate('Night'), '', -1);
            IPS_SetVariableProfileAssociation('SecuritySystem.HomeKit', 3, $this->Translate('Disarm'), '', -1);
        }
    }

    public function Migrate(string $JSONData): string
    {

        //Never delete this line!
        parent::Migrate($JSONData);

        //Merges the legacy per-type accessory properties into the unified 'Accessories' property
        //and moves the configuration number into an attribute
        $persistence = HomeKitManager::migratePersistence(json_decode($JSONData, true));

        if ($persistence === null) {
            return '';
        }

        return json_encode($persistence);
    }

    public function GetCompatibleParents(): string
    {

        //Each bridge requires its own ServerSocket
        return json_encode([
            'type'      => 'require',
            'moduleIDs' => ['{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}']
        ]);
    }

    public function GetConfigurationForParent(): string
    {
        return json_encode([
            'Port'   => $this->ReadPropertyInteger('BridgePort'),
            'UseSSL' => false,
        ]);
    }

    public function GetConfigurationForm(): string
    {
        $hasPairings = $this->pairings->hasPairings();

        $pairButton = [
            'type'    => 'Button',
            'caption' => $hasPairings ? 'Pair again' : 'Pair now',
            'onClick' => 'HK_RestartPairing($id);'
        ];

        if ($hasPairings) {
            $pairButton['confirm'] = 'Pairing again will remove the current pairing and create a new one. Only one pairing can exist at a time. To share your HomeKit devices with other people, invite them to your HomeKit home.';
        }

        $dnssd = [
            [
                'type'  => 'RowLayout',
                'items' => [
                    $pairButton,
                    [
                        'type'    => 'Label',
                        'caption' => '',
                        'width'   => '20px'
                    ],
                    [
                        'type'    => 'PopupButton',
                        'caption' => 'Expert options',
                        'popup'   => [
                            'caption' => 'Expert options',
                            'items'   => [
                                [
                                    'type'    => 'Label',
                                    'caption' => 'These options are for experts only! Do not touch!'
                                ],
                                [
                                    'type'    => 'Label',
                                    'caption' => 'After changing the name please delete the old entry in the DNS-SD control'
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
                ]
            ],
            [
                'type'    => 'PopupAlert',
                'name'    => 'PairingAlert',
                'visible' => false,
                'popup'   => [
                    'caption' => 'Pair with HomeKit',
                    'items'   => [
                        [
                            'type'        => 'QrCode',
                            'name'        => 'PairingQR',
                            'source'      => '',
                            'description' => false,
                            'center'      => true
                        ],
                        [
                            'type'    => 'Label',
                            'name'    => 'PairingCode',
                            'caption' => '',
                            'bold'    => true
                        ],
                        [
                            'type'    => 'Label',
                            'caption' => 'Scan the QR code with the camera of your iOS device or enter the setup code manually in the Home app. The code is valid for 5 minutes.'
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
                'Pair now'                                                                            => 'Jetzt koppeln',
                'Pair again'                                                                          => 'Erneut koppeln',
                'Pair with HomeKit'                                                                   => 'Mit HomeKit koppeln',
                'Scan the QR code with the camera of your iOS device or enter the setup code manually in the Home app. The code is valid for 5 minutes.' => 'Den QR-Code mit der Kamera des iOS-Geräts scannen oder den Code manuell in der Home-App eingeben. Der Code ist für maximal 5 Minuten gültig.',
                'Pairing again will remove the current pairing and create a new one. Only one pairing can exist at a time. To share your HomeKit devices with other people, invite them to your HomeKit home.' => 'Erneutes Koppeln entfernt die bestehende Kopplung und erstellt eine neue. Es kann immer nur eine Kopplung gleichzeitig bestehen. Um HomeKit-Geräte mit weiteren Personen zu teilen, müssen diese in das HomeKit Zuhause eingeladen werden.',
                'These options are for experts only! Do not touch!'                                   => 'Diese Einstellungen sind nur für Experten. Nicht anfassen!',
                'Expert options'                                                                      => 'Expertenoptionen',
                'After changing the name please delete the old entry in the DNS-SD control'           => 'Nach einer Namensänderung muss der alte Eintrag im DNS-SD Control manuell gelöscht werden',
                'Our parent instance (ServerSocket) is not active!'                                   => 'Die übergeordnete Instanz (ServerSocket) ist nicht aktiv!',
                // SecuritySystem
                'Stay'   => 'Zuhause',
                'Away'   => 'Abwesend',
                'Night'  => 'Nacht',
                'Disarm' => 'Aus',
            ]
        ];

        $formFront = [
            'elements'     => [],
            'translations' => $translations
        ];

        $formBack = [
            'elements' => $dnssd
        ];

        return json_encode(array_merge_recursive($formFront, $this->manager->getConfigurationForm(), $formBack));
    }

    public function ForwardData(string $JSONString): string
    {
        return '';
    }

    public function ReceiveData(string $JSONString): string
    {
        $data = json_decode($JSONString);

        //Decode buffer (IPSModuleStrict transports the data flow HEX encoded)
        $buffer = hex2bin($data->Buffer);

        //Show some debug data
        switch ($data->Type) {
            case 0: /* Data */
                $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Received: ' . $buffer, 0);
                break;
            case 1: /* Connected */
                $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Connected', 0);
                return '';
            case 2: /* Disconnected */
                $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Disconnected', 0);
                $this->clearSession($data->ClientIP, $data->ClientPort);
                return '';
        }

        //Get Session for ClientIP/ClientPort
        $session = $this->getSession($data->ClientIP, $data->ClientPort);

        //Add new data and process it inside the session
        $response = $session->processData($buffer);

        //A controller is connected again, so the next state change while disconnected has to increment the state number
        if ($session->isEncrypted() && $this->ReadAttributeBoolean('StateNumberPending')) {
            $this->WriteAttributeBoolean('StateNumberPending', false);
        }

        //Only if we have a valid response
        if ($response !== '') {
            $this->SendDebug('HomeKit ' . $data->ClientIP . ':' . $data->ClientPort, 'Transmit: ' . $response, 0);

            //Send response
            $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => bin2hex($response), 'ClientIP' => $data->ClientIP, 'ClientPort' => $data->ClientPort, 'Type' => 0 /* Data */]));
        }

        //Save session for ClientIP/ClientPort
        $this->setSession($data->ClientIP, $data->ClientPort, $session);

        return '';
    }

    public function Cleanup(): void
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

    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        if (($Message == IPS_KERNELMESSAGE) && ($Data[0] == KR_READY)) {

            //The state number has to be incremented by one after every restart
            if ($this->pairings->hasPairings()) {
                $this->incrementStateNumber();
            }

            //Update DNSSD parameters before the service is registered
            $this->UpdateDNSSD();
        }

        //Most characteristics only need changes. Some (e.g. button presses) are relevant on every update
        if (($Message == VM_UPDATE) && ($Data[1] /* Changed */ || $this->notifiesOnUpdate($SenderID))) {
            if ($this->isControllerConnected()) {
                //Connected controllers receive events
                $this->processNotifications($SenderID, $Data[0] /* Value */, $Data[1] /* Changed */);
            } else {
                //Disconnected controllers detect changes via the state number
                $this->processDisconnectedStateChange();
            }
        }

        // Diese Zeile nicht löschen
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
    }

    public function ApplyChanges(): void
    {

        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        // Remove all References
        // updateAccessories will register the new ones
        $refs = $this->GetReferenceList();
        foreach ($refs as $ref) {
            $this->UnregisterReference($ref);
        }

        // Validate accessories and register object references
        $this->manager->updateAccessories();

        // Structure changes of the attribute database require a new configuration number
        if ($this->updateConfigurationNumber($this->manager->getAccessories())) {

            // Subscriptions might be invalid after accessories changed. Clients will resubscribe
            $this->clearSessionSubscriptions();
        }

        $this->updateMessageRegistrations();

        $this->announce();
    }

    public function RestartPairing(): string
    {

        //Check if our parent instance (ServerSocket) is connected and active
        $pid = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if (($pid == 0) || (IPS_GetInstance($pid)['InstanceStatus'] != 102 /* IS_ACTIVE */)) {
            echo $this->Translate('Our parent instance (ServerSocket) is not active!');

            return '';
        }

        //The setup ID links the QR code to this bridge via the sh TXT record
        $setupID = '';
        for ($i = 0; $i < 4; $i++) {
            $setupID .= substr('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', random_int(0, 35), 1);
        }
        $this->WriteAttributeString('SetupID', $setupID);

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

        //Reformat to new UI format of newer iOS Versions (leave old format as-is for correct salt calculation)
        $displayCode = str_replace('-', '', $setupCode);
        $displayCode = substr($displayCode, 0, 4) . '-' . substr($displayCode, 4, 4);

        //Show the QR code and the setup code in the pairing popup
        $this->UpdateFormField('PairingQR', 'source', $this->buildSetupURI($setupCode, $setupID));
        $this->UpdateFormField('PairingCode', 'caption', $displayCode);
        $this->UpdateFormField('PairingAlert', 'visible', true);

        return $displayCode;
    }

    //Builds the X-HM:// setup URI which iOS devices can pair from via QR code
    private function buildSetupURI(string $setupCode, string $setupID): string
    {

        //45-bit payload: version (3 bit) | reserved (4 bit) | category (8 bit) | flags (4 bit) | setup code (27 bit)
        $payload = 0;
        $payload |= 2 /* Bridge */ << 31;
        $payload |= (1 << 1) /* IP pairing */ << 27;
        $payload |= intval(str_replace('-', '', $setupCode)) & 0x7FFFFFF;

        //Encode as base36, padded to 9 characters
        $encoded = strtoupper(str_pad(base_convert(strval($payload), 10, 36), 9, '0', STR_PAD_LEFT));

        return 'X-HM://' . $encoded . $setupID;
    }

    // This is just required for testing
    protected function getTime(): int
    {
        return time();
    }

    private function UpdateDNSSD(): void
    {

        // Verify name compliance
        if (ctype_alnum($this->ReadPropertyString('BridgeName')) === false) {
            echo $this->Translate('Name is required to consist only of letters and numbers!');
        }

        // Verify id compliance
        if (filter_var($this->ReadPropertyString('BridgeID'), FILTER_VALIDATE_MAC) === false) {
            echo $this->Translate('ID is not a valid MAC style address!');
        }

        // Update DNSSD Service parameters. Publishing them is done via AnnounceService
        $this->UpdateService(
            $this->ReadPropertyString('BridgeName'),
            '_hap._tcp',
            '',
            '',
            $this->ReadPropertyInteger('BridgePort'),
            $this->buildTXTRecords()
        );
    }

    private function buildTXTRecords(): array
    {
        $txtRecords = [
            'md=' . $this->ReadPropertyString('BridgeName'),
            'pv=1.1',
            'id=' . $this->ReadPropertyString('BridgeID'),
            'c#=' . $this->ReadAttributeInteger('ConfigurationNumber'),
            's#=' . $this->ReadAttributeInteger('CurrentStateNumber'),
            'ff=0', /* Switch to 1 when we have a MFi certificate */
            'ci=2',
            'sf=' . ($this->pairings->hasPairings() ? '0' : '1') /* Do not allow more than one pairing */
        ];

        //Lets iOS devices match the setup QR code to this bridge
        $setupID = $this->ReadAttributeString('SetupID');
        if ($setupID != '') {
            $txtRecords[] = 'sh=' . base64_encode(substr(hash('sha512', $setupID . $this->ReadPropertyString('BridgeID'), true), 0, 4));
        }

        return $txtRecords;
    }

    private function clearSession(string $clientIP, int $clientPort): void
    {
        $this->SetBuffer($clientIP . ':' . $clientPort, '');
    }

    private function getSession(string $clientIP, int $clientPort): HomeKitSession
    {
        $data = $this->GetBuffer($clientIP . ':' . $clientPort);

        $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Read Session: ' . $data, 0);

        return new HomeKitSession(
            function ($Message, $Data, $Type)
            {
                $this->SendDebug($Message, $Data, $Type);
            },
            function ($VariableID)
            {
                $this->RegisterMessage($VariableID, VM_UPDATE);
            },
            $this->pairings,
            $this->codes,
            $this->manager,
            $this->ReadPropertyString('BridgeID'),
            hex2bin($this->ReadPropertyString('AccessoryKeyPair')),
            $data,
            function ($Identifier)
            {
                $this->terminateSessions($Identifier);
            }
        );
    }

    private function setSession(string $clientIP, int $clientPort, HomeKitSession $session): void
    {
        $data = $session->__toString();

        $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Write Session: ' . $data, 0);

        if (!($session instanceof HomeKitSession)) {
            throw new Exception('HomeKitSession expected as parameter type!');
        }

        $this->SetBuffer($clientIP . ':' . $clientPort, $data);
    }

    private function terminateSessions(string $Identifier): void
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

    private function processNotifications(int $VariableID, mixed $Value, bool $Changed): void
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
                $response = $session->notifyVariable($VariableID, $Value, $Changed);

                //Only if we have a valid response
                if ($response !== '') {
                    $this->SendDebug('HomeKit ' . $clientIP . ':' . $clientPort, 'Transmit: ' . $response, 0);

                    //Send response
                    $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => bin2hex($response), 'ClientIP' => $clientIP, 'ClientPort' => intval($clientPort), 'Type' => 0 /* Data */]));
                }

                //Save session for ClientIP/ClientPort
                $this->setSession($clientIP, intval($clientPort), $session);
            }
        }
    }

    private function processDisconnectedStateChange(): void
    {

        //Only paired accessories advertise state changes, and only once until a controller connects again
        if (!$this->pairings->hasPairings() || $this->ReadAttributeBoolean('StateNumberPending')) {
            return;
        }

        $this->WriteAttributeBoolean('StateNumberPending', true);
        $this->incrementStateNumber();

        $this->UpdateDNSSD();
        $this->AnnounceService();
    }

    private function incrementStateNumber(): void
    {

        //Must have a range of 1-65535 and wrap to 1 when it overflows
        $this->WriteAttributeInteger('CurrentStateNumber', ($this->ReadAttributeInteger('CurrentStateNumber') % 65535) + 1);
    }

    private function updateConfigurationNumber(array $export): bool
    {
        $hash = HomeKitManager::getConfigurationHash($export);
        if ($this->ReadAttributeString('ConfigurationHash') == $hash) {
            return false;
        }

        $this->WriteAttributeString('ConfigurationHash', $hash);

        //Must have a range of 1-65535 and wrap to 1 when it overflows
        $this->WriteAttributeInteger('ConfigurationNumber', ($this->ReadAttributeInteger('ConfigurationNumber') % 65535) + 1);

        return true;
    }

    //Called while serving the attribute database to a controller
    private function onAccessoriesExported(array $export): void
    {
        if ($this->updateConfigurationNumber($export)) {
            $this->updateMessageRegistrations();
            $this->announce();
        }
    }

    //Only variables backing a characteristic with notifications are relevant for events and the state number
    private function updateMessageRegistrations(): void
    {
        $variableIDs = $this->manager->getNotifyVariableIDs();

        //Kept for MessageSink, which must not build all accessories on every update
        $this->SetBuffer('UpdateNotifyVariables', json_encode($this->manager->getNotifyVariableIDs(true)));

        foreach ($this->GetMessageList() as $senderID => $messages) {
            if (in_array(VM_UPDATE, $messages) && !in_array($senderID, $variableIDs)) {
                $this->UnregisterMessage($senderID, VM_UPDATE);
            }
        }

        foreach ($variableIDs as $variableID) {
            $this->RegisterMessage($variableID, VM_UPDATE);
        }
    }

    private function notifiesOnUpdate(int $VariableID): bool
    {
        return in_array($VariableID, json_decode($this->GetBuffer('UpdateNotifyVariables'), true) ?: []);
    }

    //A controller is connected, as soon as at least one secure session exists
    private function isControllerConnected(): bool
    {
        foreach ($this->GetBufferList() as $name) {
            //check for a colon, which indicates an ip / port combination
            if (strpos($name, ':') === false) {
                continue;
            }

            $json = json_decode($this->GetBuffer($name), true);
            if (($json['encrypted'] ?? false) && !($json['locked'] ?? false)) {
                return true;
            }
        }

        return false;
    }

    private function announce(): void
    {

        //Re-publishing changed records while no controller is connected is a state change as well
        if ((IPS_GetKernelRunlevel() == KR_READY) && $this->pairings->hasPairings() && !$this->isControllerConnected()) {
            $withoutStateNumber = function (array $records)
            {
                return array_values(array_filter($records, function ($record)
                {
                    return strpos($record, 's#=') !== 0;
                }));
            };

            $published = $this->GetPublishedTXTRecords($this->ReadPropertyString('BridgeName'));
            if (($published !== null) && ($withoutStateNumber($published) != $withoutStateNumber($this->buildTXTRecords()))) {
                $this->incrementStateNumber();
            }
        }

        $this->UpdateDNSSD();
        $this->AnnounceService();
    }

    private function clearSessionSubscriptions(): void
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
