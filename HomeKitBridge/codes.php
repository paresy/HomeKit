<?php

declare(strict_types=1);

class HomeKitCodes
{
    private $instanceID = 0;
    private $sendDebug = null;
    private $getBuffer = null;
    private $setBuffer = null;

    private function SendDebug(string $message): void
    {
        ($this->sendDebug)('HomeKitCodes', $message, 0);
    }

    public function __construct(int $instanceID, callable $sendDebug, callable $getBuffer, callable $setBuffer)
    {
        $this->instanceID = $instanceID;
        $this->sendDebug = $sendDebug;
        $this->getBuffer = $getBuffer;
        $this->setBuffer = $setBuffer;
    }

    private function isValidSetupCode(string $setupCode): bool
    {
        return !in_array($setupCode, [
            '0000-0000',
            '1111-1111',
            '2222-2222',
            '3333-3333',
            '4444-4444',
            '5555-5555',
            '6666-6666',
            '7777-7777',
            '8888-8888',
            '9999-9999',
            '1234-5678',
            '8765-4321'
        ]);
    }

    public function generateSetupCode(): string
    {
        $code = '0000-0000';

        while (!$this->isValidSetupCode($code)) {
            $number = sprintf('%08d', random_int(0, 99999999));
            $code = substr($number, 0, 4) . '-' . substr($number, 4, 4);
        }

        //The code expires after 5 minutes or if a new one is generated
        $setupCode = [
            'expires' => time() + 5 * 60,
            'code'    => $code
        ];

        ($this->setBuffer)('SetupCode', json_encode($setupCode));

        return $code;
    }

    public function getSetupCode(): string
    {
        $setupCode = ($this->getBuffer)('SetupCode');

        if ($setupCode == '') {
            return '';
        }

        $setupCode = json_decode($setupCode, true);

        if (time() > $setupCode['expires']) {
            return '';
        }

        $this->SendDebug('Getting current setup code: ' . $setupCode['code']);

        return $setupCode['code'];
    }

    public function removeSetupCode(): void
    {
        $code = $this->getSetupCode();

        if ($code === null) {
            return;
        }

        ($this->setBuffer)('SetupCode', '');

        $this->SendDebug('Removing current setup code: ' . $code);
    }
}
