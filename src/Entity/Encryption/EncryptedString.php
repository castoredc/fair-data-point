<?php
declare(strict_types=1);

namespace App\Entity\Encryption;

use JsonSerializable;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class EncryptedString implements JsonSerializable
{
    public function __construct(private string $cipherText, private string $nonce)
    {
    }

    public function getCipherText(): string
    {
        return $this->cipherText;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'cipherText' => $this->cipherText,
            'nonce' => $this->nonce,
        ];
    }

    public static function fromJsonString(string $serializedJson): self
    {
        $decodedJson = json_decode($serializedJson, true, 512, JSON_THROW_ON_ERROR);

        return new self($decodedJson['cipherText'], $decodedJson['nonce']);
    }
}
