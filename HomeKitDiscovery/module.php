<?php

    include_once __DIR__ . '/../libs/vendor/autoload.php';

    use LibDNS\Decoder\DecoderFactory;
    use LibDNS\Encoder\EncoderFactory;
    use LibDNS\Messages\MessageFactory;
    use LibDNS\Messages\MessageTypes;
    use LibDNS\Records\RDataFactory;
    use LibDNS\Records\ResourceClasses;
    use LibDNS\Records\ResourceFactory;
    use LibDNS\Records\ResourceTypes;
    use LibDNS\Records\TypeDefinitions\FieldDefinitionFactory;
    use LibDNS\Records\TypeDefinitions\TypeDefinitionFactory;
    use LibDNS\Records\Types\TypeFactory;
    use LibDNS\Records\Types\Types;

    class HomeKitDiscovery extends IPSModule
    {
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            //Register some properties
            $this->RegisterPropertyString('BridgeID', implode(':', array_slice(str_split(strtoupper(md5(IPS_GetLicensee())), 2), 0, 6)));
            $this->RegisterPropertyString('BridgeName', 'Symcon');
            $this->RegisterPropertyInteger('BridgePort', 34587);

            //Multicast Socket
            $this->RequireParent('{BAB408E0-0A0F-48C3-B14E-9FB2FA81F66A}');

            //Set simple filter on _hap messages to reduce load
            $this->SetReceiveDataFilter('.*_hap.*');

            //Self-announce ourselves each 1 minutes (TTL is 2 minutes)
            $this->RegisterTimer('Announce', 60 * 1000, "HK_AnnounceBridge(\$_IPS['TARGET']);");
        }

        public function GetConfigurationForParent()
        {
            return json_encode([
                'Host'               => '224.0.0.251',
                'Port'               => 5353,
                'BindPort'           => 5353,
                'MulticastIP'        => '224.0.0.251',
                'EnableBroadcast'    => true,
                'EnableReuseAddress' => true,
                'EnableLoopback'     => true
            ]);
        }

        public function ApplyChanges()
        {

            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            // Verify name compliance
            if (ctype_alnum($this->ReadPropertyString('BridgeName')) === false) {
                echo $this->Translate('Name is required to consist only of letters and numbers!');
            }

            // Verify id compliance
            if (filter_var($this->ReadPropertyString('BridgeID'), FILTER_VALIDATE_MAC) === false) {
                echo $this->Translate('ID is not a valid MAC style address!');
            }
        }

        public function AnnounceBridge()
        {
            $id = $this->ReadPropertyString('BridgeID');
            $name = $this->ReadPropertyString('BridgeName');

            //List all bridges
            $ids = IPS_GetInstanceListByModuleID('{7FC71134-CFD0-4909-819C-B794FE067FBC}');

            //Fetch current configuration number for our bridge
            $number = null;
            foreach ($ids as $iid) {
                if (IPS_GetProperty($iid, 'DiscoveryInstanceID') == $this->InstanceID) {
                    $number = IPS_GetProperty($iid, 'ConfigurationNumber');
                    break;
                }
            }

            if ($number == null) {
                throw new Exception($this->Translate('Cannot find associated HomeKit bridge!'));
            }

            //Detect configuration for our network interface
            $parentID = IPS_GetInstance($this->InstanceID)['ConnectionID'];
            $bindIP = IPS_GetProperty($parentID, 'BindIP');

            //We provide the port from our configuration
            $bindPort = $this->ReadPropertyInteger('BridgePort');

            // Create response message
            $response = (new MessageFactory())->create(MessageTypes::RESPONSE);
            $response->isAuthoritative(true);
            $response->isRecursionDesired(false);

            // Add answer TXT
            $af = (new FieldDefinitionFactory());
            $at = (new TypeDefinitionFactory())->create($af, [
                'md' => Types::CHARACTER_STRING, //Model Name
                'id' => Types::CHARACTER_STRING, //Identifier derived from Licence E-Mail
                'cs' => Types::CHARACTER_STRING, //Current Configuration Number
                'ss' => Types::CHARACTER_STRING, //Current State Number
                'ff' => Types::CHARACTER_STRING, //Feature Flags
                'ci' => Types::CHARACTER_STRING, //Accessory Category Identifier (2=Bridge)
                'sf' => Types::CHARACTER_STRING  //Status Flag
            ]);
            $ad = (new RDataFactory())->create($at);
            $a = (new ResourceFactory())->create(ResourceTypes::TXT, $ad);
            $a->setName($name . '._hap._tcp.local');
            $a->setClass(ResourceClasses::IN | 0x8000 /* CACHE FLUSH */);
            $a->setTTL(4500);
            $a->getData()->setFieldByName('md', (new TypeFactory())->createCharacterString('md=' . $name));
            $a->getData()->setFieldByName('id', (new TypeFactory())->createCharacterString('id=' . $id));
            $a->getData()->setFieldByName('cs', (new TypeFactory())->createCharacterString('c#=' . $number));
            $a->getData()->setFieldByName('ss', (new TypeFactory())->createCharacterString('s#=1'));
            $a->getData()->setFieldByName('ff', (new TypeFactory())->createCharacterString('ff=0'));
            $a->getData()->setFieldByName('ci', (new TypeFactory())->createCharacterString('ci=2'));
            $a->getData()->setFieldByName('sf', (new TypeFactory())->createCharacterString('sf=1'));
            $response->getAnswerRecords()->add($a);

            // Add answer PTR
            $at = (new TypeDefinitionFactory())->create($af, [
                'ptr' => Types::DOMAIN_NAME, //Domain Name Pointer
            ]);
            $ad = (new RDataFactory())->create($at);
            $a = (new ResourceFactory())->create(ResourceTypes::PTR, $ad);
            $a->setName('_services._dns-sd._udp.local');
            $a->setClass(ResourceClasses::IN);
            $a->setTTL(4500);
            $a->getData()->setFieldByName('ptr', (new TypeFactory())->createDomainName('_hap._tcp.local'));
            $response->getAnswerRecords()->add($a);

            // Add answer PTR #2
            $at = (new TypeDefinitionFactory())->create($af, [
                'ptr' => Types::DOMAIN_NAME, //Domain Name Pointer
            ]);
            $ad = (new RDataFactory())->create($at);
            $a = (new ResourceFactory())->create(ResourceTypes::PTR, $ad);
            $a->setName('_hap._tcp.local');
            $a->setClass(ResourceClasses::IN);
            $a->setTTL(4500);
            $a->getData()->setFieldByName('ptr', (new TypeFactory())->createDomainName($name . '._hap._tcp.local'));
            $response->getAnswerRecords()->add($a);

            // Add answer A
            $at = (new TypeDefinitionFactory())->create($af, [
                'ipv4' => Types::IPV4_ADDRESS, //Domain Name Pointer
            ]);
            $ad = (new RDataFactory())->create($at);
            $a = (new ResourceFactory())->create(ResourceTypes::A, $ad);
            $a->setName('hap.local');
            $a->setClass(ResourceClasses::IN | 0x8000 /* CACHE FLUSH */);
            $a->setTTL(240);
            $a->getData()->setFieldByName('ipv4', (new TypeFactory())->createIPv4Address($bindIP));
            $response->getAnswerRecords()->add($a);

            // Add answer SRV
            $at = (new TypeDefinitionFactory())->create($af, [
                'priority' => Types::SHORT, //Domain Name Pointer,
                'weight'   => Types::SHORT, //Domain Name Pointer,
                'port'     => Types::SHORT, //Domain Name Pointer,
                'target'   => Types::DOMAIN_NAME, //Domain Name Pointer,
            ]);
            $ad = (new RDataFactory())->create($at);
            $a = (new ResourceFactory())->create(ResourceTypes::SRV, $ad);
            $a->setName($name . '._hap._tcp.local');
            $a->setClass(ResourceClasses::IN | 0x8000 /* CACHE FLUSH */);
            $a->setTTL(120);
            $a->getData()->setFieldByName('priority', (new TypeFactory())->createShort(0));
            $a->getData()->setFieldByName('weight', (new TypeFactory())->createShort(0));
            $a->getData()->setFieldByName('port', (new TypeFactory())->createShort($bindPort));
            $a->getData()->setFieldByName('target', (new TypeFactory())->createDomainName('hap.local'));
            $response->getAnswerRecords()->add($a);

            // Encode response message
            $encoder = (new EncoderFactory())->create();
            $response = utf8_encode($encoder->encode($response));
            $this->SendDataToParent(json_encode(['DataID' => '{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}', 'Buffer' => $response]));
        }

        public function ReceiveData($JSONString)
        {
            $data = json_decode($JSONString);
            $packet = utf8_decode($data->Buffer);

            if (substr($packet, 0, 4) == "\x00\x00\x00\x00" && strpos($packet, '_hap' . chr(0x04) . '_tcp' . chr(0x05) . 'local') !== false) {
                $this->SendDebug('Responding to HAP Query', $packet, 0);
                $this->AnnounceBridge();
            }

            /*
                        //FIXME: Fix errors in DNS parsing library
            
                        $decoder = (new DecoderFactory)->create();
                        $query = $decoder->decode($packet);
            
                        //Check if this is a query
                        if($query->getType() == MessageTypes::QUERY) {
            
                            //Check if we have a question
                            $q = $query->getQuestionRecords();
                            if($q->count() == 1) {
            
                                $r = $q->current();
                                $n = $r->getName();
            
                                $this->SendDebug("Received Query", $packet, 0);
            
                                //Check if question is an HAP message
                                if($n->__toString() == "_hap._tcp.local") {
            
                                    $this->SendDebug("Responding to HAP Query", $packet, 0);
            
                                    //Build and send the response
                                    $this->AnnounceBridge();
            
                                }
            
                            }
            
                        }
            */
        }
    }
