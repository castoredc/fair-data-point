<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\Enum\DistributionType;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use function boolval;

#[Assert\GroupSequenceProvider]
class DistributionApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    #[Assert\NotBlank]
    #[Assert\Choice(['rdf', 'csv'])]
    private string $type;

    /**
     * @AppAssert\Slug(type="App\Entity\FAIRData\Distribution")
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $slug;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $defaultMetadataModel;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $license;

    #[Assert\NotNull(groups: ['csv'])]
    #[Assert\Type('bool')]
    private ?bool $includeAllData = null;

    #[Assert\NotBlank(groups: ['rdf'])]
    #[Assert\Type('string')]
    private ?string $dataModel = null;

    #[Assert\NotBlank(groups: ['rdf'])]
    #[Assert\Type('string')]
    private ?string $dataModelVersion = null;

    #[Assert\NotBlank(groups: ['csv'])]
    #[Assert\Type('string')]
    private ?string $dataDictionary = null;

    #[Assert\NotBlank(groups: ['csv'])]
    #[Assert\Type('string')]
    private ?string $dataDictionaryVersion = null;

    #[Assert\Type('string')]
    private ?string $apiUser = null;

    #[Assert\Type('string')]
    private ?string $clientId = null;

    #[Assert\Type('string')]
    private ?string $clientSecret = null;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    private bool $published;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    private bool $cached;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    private bool $public;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
        $this->slug = $this->getFromData('slug');
        $this->defaultMetadataModel = $this->getFromData('defaultMetadataModel');
        $this->license = $this->getFromData('license');
        $this->includeAllData = $this->getFromData('includeAllData');
        $this->dataModel = $this->getFromData('dataModel');
        $this->dataModelVersion = $this->getFromData('dataModelVersion');
        $this->apiUser = $this->getFromData('apiUser');
        $this->clientId = $this->getFromData('clientId');
        $this->clientSecret = $this->getFromData('clientSecret');
        $this->published = boolval($this->getFromData('published'));
        $this->cached = boolval($this->getFromData('cached'));
        $this->public = boolval($this->getFromData('public'));
    }

    public function getType(): DistributionType
    {
        return DistributionType::fromString($this->type);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDefaultMetadataModel(): string
    {
        return $this->defaultMetadataModel;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getIncludeAllData(): ?bool
    {
        return $this->includeAllData;
    }

    public function getDataModel(): ?string
    {
        return $this->dataModel;
    }

    public function getDataModelVersion(): ?string
    {
        return $this->dataModelVersion;
    }

    public function getDataDictionary(): ?string
    {
        return $this->dataDictionary;
    }

    public function getDataDictionaryVersion(): ?string
    {
        return $this->dataDictionaryVersion;
    }

    public function getApiUser(): ?string
    {
        return $this->apiUser;
    }

    public function getClientId(): ?SensitiveDataString
    {
        return $this->clientId !== null ? new SensitiveDataString($this->clientId) : null;
    }

    public function getClientSecret(): ?SensitiveDataString
    {
        return $this->clientSecret !== null ? new SensitiveDataString($this->clientSecret) : null;
    }

    public function getPublished(): bool
    {
        return $this->published;
    }

    public function isCached(): bool
    {
        return $this->cached;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function getGroupSequence(): array|Assert\GroupSequence
    {
        return [
            'DistributionApiRequest',
            $this->getType()->toString(),
        ];
    }
}
