<?php
declare(strict_types=1);

namespace App\Command\Castor;

final class UpdateCastorServerCommand
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $url,
        private string $flag,
        private bool $default,
        private string $clientId,
        private string $clientSecret,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}
