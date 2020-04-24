<?php
declare(strict_types=1);

namespace App\Encryption;

use JsonSerializable;
use const JSON_THROW_ON_ERROR;
use function json_decode;

class EncryptedString implements JsonSerializable
{
    /** @var string */
    private $cipherText;

    /** @var string */
    private $nonce;

    public function __construct(string $cipherText, string $nonce)
    {
        $this->cipherText = $cipherText;
        $this->nonce = $nonce;
    }

    public function getCipherText(): string
    {
        return $this->cipherText;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * @return mixed[]
     */
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
