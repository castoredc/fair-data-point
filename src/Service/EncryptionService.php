<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Encryption\EncryptedString;
use App\Entity\Encryption\SensitiveDataString;
use App\Exception\CouldNotDecrypt;
use function random_bytes;
use function sodium_bin2hex;
use function sodium_crypto_secretbox;
use function sodium_crypto_secretbox_open;
use function sodium_hex2bin;
use const SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

class EncryptionService
{
    private string $encryptionKey;

    public function __construct(string $encryptionKey = '')
    {
        $this->encryptionKey = sodium_hex2bin($encryptionKey);
    }

    public function encrypt(SensitiveDataString $plainText): EncryptedString
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($plainText->exposeAsString(), $nonce, $this->encryptionKey);

        return new EncryptedString(sodium_bin2hex($ciphertext), sodium_bin2hex($nonce));
    }

    public function decrypt(EncryptedString $encryptedString): SensitiveDataString
    {
        $plainText = sodium_crypto_secretbox_open(
            sodium_hex2bin($encryptedString->getCipherText()),
            sodium_hex2bin($encryptedString->getNonce()),
            $this->encryptionKey
        );

        if ($plainText === false) {
            throw new CouldNotDecrypt();
        }

        return new SensitiveDataString($plainText);
    }
}
