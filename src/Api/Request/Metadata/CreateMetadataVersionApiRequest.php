<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\VersionType;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMetadataVersionApiRequest extends SingleApiRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $versionType;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $model;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $modelVersion;

    protected function parse(): void
    {
        $this->versionType = $this->getFromData('versionType');
        $this->model = $this->getFromData('model');
        $this->modelVersion = $this->getFromData('modelVersion');
    }

    public function getVersionType(): VersionType
    {
        return VersionType::fromString($this->versionType);
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getModelVersion(): string
    {
        return $this->modelVersion;
    }
}
