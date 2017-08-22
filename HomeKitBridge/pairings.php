<?php declare(strict_types=1);

class HomeKitPairings
{
    private $instanceID = 0;
    private $sendDebug = null;

    private function SendDebug(string $message): void
    {
        ($this->sendDebug)('HomeKitPairings', $message, 0);
    }

    public function __construct(int $InstanceID, callable $sendDebug)
    {
        $this->instanceID = $InstanceID;
        $this->sendDebug = $sendDebug;
    }

    public function addPairing(string $identifier, string $publicKey, int $permissions): void
    {
        $pairings = json_decode(IPS_GetProperty($this->instanceID, 'Pairings'), true);

        $pairings[$identifier] = [
            'publicKey'   => bin2hex($publicKey),
            'permissions' => $permissions
        ];

        IPS_SetProperty($this->instanceID, 'Pairings', json_encode($pairings));
        IPS_ApplyChanges($this->instanceID);

        $this->SendDebug('Saving pairing for identifier: ' . $identifier);
    }

    public function removePairing(string $identifier): void
    {
        $pairings = json_decode(IPS_GetProperty($this->instanceID, 'Pairings'), true);

        $this->SendDebug('Removing pairing for identifier: ' . $identifier);

        if (isset($pairings[$identifier])) {
            unset($pairings[$identifier]);
        }

        IPS_SetProperty($this->instanceID, 'Pairings', json_encode($pairings));
        IPS_ApplyChanges($this->instanceID);
    }

    public function listPairings(): array
    {
        $this->SendDebug('Requesting list of pairings');

        return json_decode(IPS_GetProperty($this->instanceID, 'Pairings'), true);
    }

    public function getPairingPublicKey(string $identifier): string
    {
        $pairings = json_decode(IPS_GetProperty($this->instanceID, 'Pairings'), true);

        $this->SendDebug('Loading pairing public key for identifier: ' . $identifier);

        if (!isset($pairings[$identifier])) {
            return "";
        }

        return hex2bin($pairings[$identifier]['publicKey']);
    }

    public function getPairingPermissions(string $identifier): string
    {
        $pairings = json_decode(IPS_GetProperty($this->instanceID, 'Pairings'), true);

        $this->SendDebug('Loading pairing permissions for identifier: ' . $identifier);

        if (!isset($pairings[$identifier])) {
            return "";
        }

        return hex2bin($pairings[$identifier]['permissions']);
    }
}
