<?php
namespace App\Services;



Class EncryptionService{
    public function encryptMessage(string $body){
        // Encrypt the message body using a secure encryption algorithm
        $encryptedBody = encrypt($body);
        return $encryptedBody;
    }

    public function decryptMessage(string $encryptedBody){
        // Decrypt the message body using the same algorithm
        $decryptedBody = decrypt($encryptedBody);
        return $decryptedBody;
    }

    public function generateKeyPair(){
        // Generate a public/private key pair for asymmetric encryption
        $privateKey = openssl_pkey_new(['private_key_bits' => 2048]);
        $publicKey = openssl_pkey_get_details($privateKey)['key'];
        return ['private_key' => $privateKey, 'public_key' => $publicKey];
    }
}
