<?php

declare(strict_types=1);

include_once __DIR__ . '/../HomeKitBridge/srp.php';

use PHPUnit\Framework\TestCase;

class SRP6aServerTest extends TestCase
{
    public function testGeneration(): void
    {
        //Salt
        $s = 'BEB25379 D1A8581E B5A72767 3A2441EE';
        $s = hex2bin($this->clean($s));

        //Username
        $I = 'alice';

        //Password
        $p = 'password123';

        //Private Value (b) - Should be random
        $b = 'E487CB59 D31AC550 471E81F0 0F6928E0 1DDA08E9 74A004F4 9E61F5D1 05284D20';
        $b = hex2bin($this->clean($b));

        $srp = new SRP6aServer($s, $I, $p, $b);

        //Precalculated B
        $Bverify = '40F57088 A482D4C7 733384FE 0D301FDD CA9080AD 7D4F6FDF 09A01006 C3CB6D56
                    2E41639A E8FA21DE 3B5DBA75 85B27558 9BDB2798 63C56280 7B2B9908 3CD1429C
                    DBE89E25 BFBD7E3C AD3173B2 E3C5A0B1 74DA6D53 91E6A06E 465F037A 40062548
                    39A56BF7 6DA84B1C 94E0AE20 8576156F E5C140A4 BA4FFC9E 38C3B07B 88845FC6
                    F7DDDA93 381FE0CA 6084C4CD 2D336E54 51C464CC B6EC65E7 D16E548A 273E8262
                    84AF2559 B6264274 215960FF F47BDD63 D3AFF064 D6137AF7 69661C9D 4FEE4738
                    2603C88E AA098058 1D077584 61B777E4 356DDA58 35198B51 FEEA308D 70F75450
                    B71675C0 8C7D8302 FD7539DD 1FF2A11C B4258AA7 0D234436 AA42B6A0 615F3F91
                    5D55CC3B 966B2716 B36E4D1A 06CE5E5D 2EA3BEE5 A1270E87 51DA45B6 0B997B0F
                    FDB0F996 2FEE4F03 BEE780BA 0A845B1D 92714217 83AE6601 A61EA2E3 42E4F2E8
                    BC935A40 9EAD19F2 21BD1B74 E2964DD1 9FC845F6 0EFC0933 8B60B6B2 56D8CAC8
                    89CCA306 CC370A0B 18C8B886 E95DA0AF 5235FEF4 393020D2 B7F30569 04759042';
        $Bverify = hex2bin($this->clean($Bverify));

        //Verify B
        $this->assertEquals($Bverify, $srp->createPublicValue());

        //Precalculated A (normally received from client)
        $Averify = 'FAB6F5D2 615D1E32 3512E799 1CC37443 F487DA60 4CA8C923 0FCB04E5 41DCE628
                    0B27CA46 80B0374F 179DC3BD C7553FE6 2459798C 701AD864 A91390A2 8C93B644
                    ADBF9C00 745B942B 79F9012A 21B9B787 82319D83 A1F83628 66FBD6F4 6BFC0DDB
                    2E1AB6E4 B45A9906 B82E37F0 5D6F97F6 A3EB6E18 2079759C 4F684783 7B62321A
                    C1B4FA68 641FCB4B B98DD697 A0C73641 385F4BAB 25B79358 4CC39FC8 D48D4BD8
                    67A9A3C1 0F8EA121 70268E34 FE3BBE6F F89998D6 0DA2F3E4 283CBEC1 393D52AF
                    724A5723 0C604E9F BCE583D7 613E6BFF D67596AD 121A8707 EEC46944 95703368
                    6A155F64 4D5C5863 B48F61BD BF19A53E AB6DAD0A 186B8C15 2E5F5D8C AD4B0EF8
                    AA4EA500 8834C3CD 342E5E0F 167AD045 92CD8BD2 79639398 EF9E114D FAAAB919
                    E14E8509 89224DDD 98576D79 385D2210 902E9F9B 1F2D86CF A47EE244 635465F7
                    1058421A 0184BE51 DD10CC9D 079E6F16 04E7AA9B 7CF7883C 7D4CE12B 06EBE160
                    81E23F27 A231D184 32D7D1BB 55C28AE2 1FFCF005 F57528D1 5A88881B B3BBB7FE';
        $Averify = hex2bin($this->clean($Averify));

        //Precalculated S
        $Sverify = 'F1036FEC D017C823 9C0D5AF7 E0FCF0D4 08B009E3 6411618A 60B23AAB BFC38339
                    72682312 14BAACDC 94CA1C53 F442FB51 C1B027C3 18AE238E 16414D60 D1881B66
                    486ADE10 ED02BA33 D098F6CE 9BCF1BB0 C46CA2C4 7F2F174C 59A9C61E 2560899B
                    83EF6113 1E6FB30B 714F4E43 B735C9FE 6080477C 1B83E409 3E4D456B 9BCA492C
                    F9339D45 BC42E67C E6C02C24 3E49F5DA 42A869EC 855780E8 4207B8A1 EA6501C4
                    78AAC0DF D3D22614 F531A00D 826B7954 AE8B14A9 85A42931 5E6DD366 4CF47181
                    496A9432 9CDE8005 CAE63C2F 9CA4969B FE840019 24037C44 6559BDBB 9DB9D4DD
                    142FBCD7 5EEF2E16 2C843065 D99E8F05 762C4DB7 ABD9DB20 3D41AC85 A58C05BD
                    4E2DBF82 2A934523 D54E0653 D376CE8B 56DCB452 7DDDC1B9 94DC7509 463A7468
                    D7F02B1B EB168571 4CE1DD1E 71808A13 7F788847 B7C6B7BF A1364474 B3B7E894
                    78954F6A 8E68D45B 85A88E4E BFEC1336 8EC0891C 3BC86CF5 00978801 78D86135
                    E7287234 58538858 D715B7B2 47406222 C1019F53 603F0169 52D49710 0858824C';
        $Sverify = hex2bin($this->clean($Sverify));

        //Verify S
        $this->assertEquals($Sverify, $srp->createPresharedSecret($Averify, $Bverify));

        //Precalculated K
        $Kverify = '5CBC219D B052138E E1148C71 CD449896 3D682549 CE91CA24 F098468F 06015BEB
                    6AF245C2 093F98C3 651BCA83 AB8CAB2B 580BBF02 184FEFDF 26142F73 DF95AC50';
        $Kverify = hex2bin($this->clean($Kverify));

        //Verify K
        $this->assertEquals($Kverify, $srp->createSessionKey($Sverify));
    }
    private function clean($s)
    {
        return str_replace([' ', "\n", "\r"], '', $s);
    }
}
