<?php

declare(strict_types=1);

include_once __DIR__ . '/../HomeKitBridge/srp.php';

use PHPUnit\Framework\TestCase;

class SRP6aTest extends TestCase
{

    public function testGeneration(): void
    {
        //Salt
        $s = random_bytes(16);

        //Username
        $I = 'hello';

        //Password
        $p = 'world';

        //Private Value (a)
        $a = random_bytes(32);

        $srpClient = new SRP6aClient($s, $I, $p, $a);

        //Public Value (A)
        $A = $srpClient->createPublicValue();

        //Private Value (b)
        $b = random_bytes(32);

        $srpServer = new SRP6aServer($s, $I, $p, $b);

        //Public Value (B)
        $B = $srpServer->createPublicValue();

        $SServer = $srpServer->createPresharedSecret($A, $B);
        $SClient = $srpClient->createPresharedSecret($A, $B);

        //Preshared secret should be equal
        $this->assertEquals($SServer, $SClient);

        $KServer = $srpServer->createSessionKey($SServer);
        $KClient = $srpClient->createSessionKey($SClient);

        //Session key should be equal
        $this->assertEquals($KServer, $KClient);

        //Client sends proof first
        $M = $srpClient->createProof($A, $B, $KClient);

        //Server verifies proof
        $this->assertTrue($srpServer->verifyProof($A, $B, $KServer, $M));

        //Server sends proof second
        $P = $srpServer->createProof($A, $M, $KClient);

        //Client verifies proof
        $this->assertTrue($srpClient->verifyProof($A, $M, $KClient, $P));

    }
}
