<?php

declare(strict_types=1);
class VirtualIO extends IPSModule
{
    private $textQueue = [];

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyBoolean('Open', false);
    }

    public function ForwardData($JSONString)
    {
        parent::ForwardData($JSONString);

        $data = json_decode($JSONString, true);

        if (!isset($data['DataID'])) {
            throw new Exception('Invalid Data packet received');
        }

        if ($data['DataID'] == '{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}') {
            $this->textQueue[] = utf8_decode($data['Buffer']);
            return;
        }

        throw new Exception('Unsupported DataID received');
    }

    public function HasText()
    {
        return count($this->textQueue) > 0;
    }

    public function PeekText()
    {
        if (!$this->HasText()) {
            throw new Exception('There is not data available');
        }

        return $this->textQueue[0];
    }

    public function PopText()
    {
        $result = $this->PeekText();
        array_shift($this->textQueue);

        return $result;
    }

    public function PushText($Text)
    {
        $this->SendDataToChildren([
            'DataID' => '{018EF6B5-AB94-40C6-AA53-46943E824ACF}',
            'Buffer' => utf8_encode($Text)
        ]);
    }
}
