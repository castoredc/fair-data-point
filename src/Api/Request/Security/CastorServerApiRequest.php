<?php
declare(strict_types=1);

namespace App\Api\Request\Security;

use App\Api\Request\SingleApiRequest;
use App\Command\Castor\UpdateCastorServerCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class CastorServerApiRequest extends SingleApiRequest
{
    private ?int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private string $url;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $flag;

    private bool $default = false;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $clientId;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $clientSecret;

    public function toCommand(): UpdateCastorServerCommand
    {
        return new UpdateCastorServerCommand(
            $this->id,
            $this->name,
            $this->url,
            $this->flag,
            $this->default,
            $this->clientId,
            $this->clientSecret
        );
    }

    protected function parse(): void
    {
        $this->id = $this->getFromData('id');
        $this->name = $this->getFromData('name');
        $this->url = $this->getFromData('url');
        $this->flag = $this->getFromData('flag');
        $this->default = $this->getFromData('default');
        $this->clientId = $this->getFromData('clientId');
        $this->clientSecret = $this->getFromData('clientSecret');
    }

    public function getId(): int
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

    public function getDefault(): ?bool
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
