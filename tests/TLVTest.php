<?php

declare(strict_types=1);

include_once __DIR__ . '/../HomeKitBridge/tlv.php';

use PHPUnit\Framework\TestCase;

class TLVTest extends TestCase
{
    public function testMethod(): void
    {
        $method = TLVBuilder::Method(TLVMethod::ListPairings);
        $tlv = new TLVParser($method);
        $tlvMethod = $tlv->getByType(TLVType::Method);

        $this->assertEquals($tlvMethod->getType(), TLVType::Method);
        $this->assertEquals($tlvMethod->getMethod(), TLVMethod::ListPairings);
    }

    public function testIdentifier(): void
    {
        $identifier = TLVBuilder::Identifier('TestIdent');
        $tlv = new TLVParser($identifier);
        $tlvIdentifier = $tlv->getByType(TLVType::Identifier);

        $this->assertEquals($tlvIdentifier->getType(), TLVType::Identifier);
        $this->assertEquals($tlvIdentifier->getIdentifier(), 'TestIdent');
    }

    public function testSalt(): void
    {
        $salt = TLVBuilder::Salt('TestSalt');
        $tlv = new TLVParser($salt);
        $tlvSalt = $tlv->getByType(TLVType::Salt);

        $this->assertEquals($tlvSalt->getType(), TLVType::Salt);
        $this->assertEquals($tlvSalt->getSalt(), 'TestSalt');
    }

    public function testPublicKey(): void
    {
        $publicKey = TLVBuilder::PublicKey('TestPublicKey');
        $tlv = new TLVParser($publicKey);
        $tlvPublicKey = $tlv->getByType(TLVType::PublicKey);

        $this->assertEquals($tlvPublicKey->getType(), TLVType::PublicKey);
        $this->assertEquals($tlvPublicKey->getPublicKey(), 'TestPublicKey');
    }

    public function testProof(): void
    {
        $proof = TLVBuilder::Proof('TestProof');
        $tlv = new TLVParser($proof);
        $tlvProof = $tlv->getByType(TLVType::Proof);

        $this->assertEquals($tlvProof->getType(), TLVType::Proof);
        $this->assertEquals($tlvProof->getProof(), 'TestProof');
    }

    public function testEncryptedData(): void
    {
        $encryptedData = TLVBuilder::EncryptedData('TestEncryptedData');
        $tlv = new TLVParser($encryptedData);
        $tlvEncryptedData = $tlv->getByType(TLVType::EncryptedData);

        $this->assertEquals($tlvEncryptedData->getType(), TLVType::EncryptedData);
        $this->assertEquals($tlvEncryptedData->getEncryptedData(), 'TestEncryptedData');
    }

    public function testState(): void
    {
        $state = TLVBuilder::State(TLVState::M1);
        $tlv = new TLVParser($state);
        $tlvState = $tlv->getByType(TLVType::State);

        $this->assertEquals($tlvState->getType(), TLVType::State);
        $this->assertEquals($tlvState->getState(), TLVState::M1);
    }

    public function testError(): void
    {
        $error = TLVBuilder::Error(TLVError::NA);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), TLVError::NA);
        $this->assertEquals($tlvError, 'N/A Error');
    }

    public function testRetryDelay(): void
    {
        $retryDelay = TLVBuilder::RetryDelay(300);
        $tlv = new TLVParser($retryDelay);
        $tlvRetryDelay = $tlv->getByType(TLVType::RetryDelay);

        $this->assertEquals($tlvRetryDelay->getType(), TLVType::RetryDelay);
        $this->assertEquals($tlvRetryDelay->getRetryDelay(), 300);
    }

    public function testCertificate(): void
    {
        $certificate = TLVBuilder::Certificate('TestCertificate');
        $tlv = new TLVParser($certificate);
        $tlvCertificate = $tlv->getByType(TLVType::Certificate);

        $this->assertEquals($tlvCertificate->getType(), TLVType::Certificate);
        $this->assertEquals($tlvCertificate->getCertificate(), 'TestCertificate');
    }

    public function testSignature(): void
    {
        $signature = TLVBuilder::Signature('TestSignature');
        $tlv = new TLVParser($signature);
        $tlvSignature = $tlv->getByType(TLVType::Signature);

        $this->assertEquals($tlvSignature->getType(), TLVType::Signature);
        $this->assertEquals($tlvSignature->getSignature(), 'TestSignature');
    }

    public function testPermissions(): void
    {
        $permissions = TLVBuilder::Permissions(TLVPermissions::Admin);
        $tlv = new TLVParser($permissions);
        $tlvPermissions = $tlv->getByType(TLVType::Permissions);

        $this->assertEquals($tlvPermissions->getType(), TLVType::Permissions);
        $this->assertEquals($tlvPermissions->getPermissions(), TLVPermissions::Admin);
        $this->assertEquals($tlvPermissions, 'Admin');
    }

    public function testSeperator(): void
    {
        $seperator = TLVBuilder::Separator();
        $tlv = new TLVParser($seperator);
        $tlvSeperator = $tlv->getByType(TLVType::Separator);

        $this->assertEquals($tlvSeperator->getType(), TLVType::Separator);
    }
}
