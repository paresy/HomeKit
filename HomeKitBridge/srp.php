<?php declare(strict_types=1);

class SRP6aServer
{
    //All function parameters are required to be binary!

    //Defined in Apple HAP 4.6.2 or RFC 5054 (3072 Bit group)
    private $N_hex = 'FFFFFFFF FFFFFFFF C90FDAA2 2168C234 C4C6628B 80DC1CD1 29024E08
                      8A67CC74 020BBEA6 3B139B22 514A0879 8E3404DD EF9519B3 CD3A431B 
                      302B0A6D F25F1437 4FE1356D 6D51C245 E485B576 625E7EC6 F44C42E9
                      A637ED6B 0BFF5CB6 F406B7ED EE386BFB 5A899FA5 AE9F2411 7C4B1FE6
                      49286651 ECE45B3D C2007CB8 A163BF05 98DA4836 1C55D39A 69163FA8
                      FD24CF5F 83655D23 DCA3AD96 1C62F356 208552BB 9ED52907 7096966D
                      670C354E 4ABC9804 F1746C08 CA18217C 32905E46 2E36CE3B E39E772C
                      180E8603 9B2783A2 EC07A28F B5C55DF0 6F4C52C9 DE2BCBF6 95581718
                      3995497C EA956AE5 15D22618 98FA0510 15728E5A 8AAAC42D AD33170D
                      04507A33 A85521AB DF1CBA64 ECFB8504 58DBEF0A 8AEA7157 5D060C7D
                      B3970F85 A6E1E4C7 ABF5AE8C DB0933D7 1E8C94E0 4A25619D CEE3D226
                      1AD2EE6B F12FFA06 D98A0864 D8760273 3EC86A64 521F2B18 177B200C
                      BBE11757 7A615D6C 770988C0 BAD946E2 08E24FA0 74E5AB31 43DB5BFC
                      E0FD108E 4B82D120 A93AD2CA FFFFFFFF FFFFFFFF';
    private $N_dec = '';

    private $g_hex = '05';
    private $g_dec = '';

    private $s_bin = '';
    private $I_bin = '';
    private $p_bin = '';
    private $b_bin = '';

    private $x_dec = '';
    private $v_dec = '';

    private $H;

    //s = Salt               (Binary)
    //I = Username           (Binary)
    //p = Cleartext Password (Binary)
    public function __construct(string $s, string $I, string $p, string $b)
    {
        $this->N_hex = str_replace([' ', "\r", "\n"], '', $this->N_hex);
        $this->N_dec = gmp_init($this->N_hex, 16);
        $this->N_bin = hex2bin($this->N_hex);
        $this->g_hex = str_replace([' ', "\r", "\n"], '', $this->g_hex);
        $this->g_dec = gmp_init($this->g_hex, 16);
        $this->g_bin = hex2bin($this->g_hex);

        $this->s_bin = $s;
        $this->I_bin = $I;
        $this->p_bin = $p;
        $this->b_bin = $b;

        $this->H = function($v) {
            return hash('sha512', $v, true);
        };

        //Private Key (x = H(s, H(I, ":", p)))
        $this->x_dec = gmp_import(($this->H)($this->s_bin . call_user_func($this->H, $this->I_bin . ':' . $this->p_bin)));

        //Verifier (c = g^x)
        $this->v_dec = gmp_powm($this->g_dec, $this->x_dec, $this->N_dec);
    }

    //We want to create public value B. We require the private value b
    public function createPublicValue(): string
    {

        //Convert to decimal
        $b_dec = gmp_import($this->b_bin);

        //Multiplier parameter (k = H(N, g))
        $k_dec = gmp_import(($this->H)($this->N_bin . str_pad($this->g_bin, strlen($this->N_bin), chr(0x00), STR_PAD_LEFT)));

        //Public Value (B = k*v + g^b)
        $B_dec = gmp_mod(gmp_add(gmp_mul($k_dec, $this->v_dec), gmp_powm($this->g_dec, $b_dec, $this->N_dec)), $this->N_dec);

        //Convert to binary
        return gmp_export($B_dec);
    }

    //Calculate Preshared Secret S
    public function createPresharedSecret($A, $B): string
    {
        $u_dec = gmp_import(($this->H)($A . $B));
        $b_dec = gmp_import($this->b_bin);
        $A_dec = gmp_import($A);

        $S_dec = gmp_powm(gmp_mul($A_dec, gmp_powm($this->v_dec, $u_dec, $this->N_dec)), $b_dec, $this->N_dec);

        return gmp_export($S_dec);
    }

    //Calculate Session Key K
    public function createSessionKey($S): string
    {
        return ($this->H)($S);
    }

    //We want to verify the proof
    public function verifyProof($A, $B, $K, $M): bool
    {

        //Proof (M = H(H(N) xor H(g), H(I), s, A, B, K))
        return $M == ($this->H)((($this->H)($this->N_bin) ^ ($this->H)($this->g_bin)) . ($this->H)($this->I_bin) . $this->s_bin . $A . $B . $K);
    }

    public function createProof($A, $M, $K)
    {
        return ($this->H)($A . $M . $K);
    }
}
