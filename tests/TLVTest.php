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
        //We want at least 256 chars to properly test split TLVs
        $key = random_bytes(1000);

        $publicKey = TLVBuilder::PublicKey($key);
        $tlv = new TLVParser($publicKey);
        $tlvPublicKey = $tlv->getByType(TLVType::PublicKey);

        $this->assertEquals($tlvPublicKey->getType(), TLVType::PublicKey);
        $this->assertEquals($tlvPublicKey->getPublicKey(), $key);
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

        $error = TLVBuilder::Error(TLVError::Unknown);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), TLVError::Unknown);
        $this->assertEquals($tlvError, 'Unknown Error');

        $error = TLVBuilder::Error(TLVError::Authentication);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), TLVError::Authentication);
        $this->assertEquals($tlvError, 'Authentication Error');

        $error = TLVBuilder::Error(TLVError::Backoff);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), TLVError::Backoff);
        $this->assertEquals($tlvError, 'Backoff Error');

        $error = TLVBuilder::Error(TLVError::MaxPeers);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), TLVError::MaxPeers);
        $this->assertEquals($tlvError, 'MaxPeers Error');

        $error = TLVBuilder::Error(TLVError::MaxTries);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), TLVError::MaxTries);
        $this->assertEquals($tlvError, 'MaxTries Error');

        $error = TLVBuilder::Error(100 /* Undefined */);
        $tlv = new TLVParser($error);
        $tlvError = $tlv->getByType(TLVType::Error);

        $this->assertEquals($tlvError->getType(), TLVType::Error);
        $this->assertEquals($tlvError->getError(), 100 /* Undefined */);
        $this->assertEquals($tlvError, 'Undefined Error');
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

        $permissions = TLVBuilder::Permissions(TLVPermissions::RegularUser);
        $tlv = new TLVParser($permissions);
        $tlvPermissions = $tlv->getByType(TLVType::Permissions);

        $this->assertEquals($tlvPermissions->getType(), TLVType::Permissions);
        $this->assertEquals($tlvPermissions->getPermissions(), TLVPermissions::RegularUser);
        $this->assertEquals($tlvPermissions, 'Regular User');

        $permissions = TLVBuilder::Permissions(100 /* Undefined */);
        $tlv = new TLVParser($permissions);
        $tlvPermissions = $tlv->getByType(TLVType::Permissions);

        $this->assertEquals($tlvPermissions->getType(), TLVType::Permissions);
        $this->assertEquals($tlvPermissions->getPermissions(), 100 /* Undefined */);
        $this->assertEquals($tlvPermissions, 'Undefined');
    }

    public function testSeperator(): void
    {
        $seperator = TLVBuilder::Separator();
        $tlv = new TLVParser($seperator);
        $tlvSeperator = $tlv->getByType(TLVType::Separator);

        $this->assertEquals($tlvSeperator->getType(), TLVType::Separator);
    }

    public function testInvalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported TLV');

        new TLVParser(chr(0xAA) . chr(0) . chr(0) . chr(0));
    }

    public function testEmpty(): void
    {
        $tlv = new TLVParser('');
        $tlvSeperator = $tlv->getByType(TLVType::Separator);

        $this->assertEquals($tlvSeperator, null);
    }
}
