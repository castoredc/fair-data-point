<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use App\Encryption\SensitiveDataString;
use App\Entity\Enum\DistributionType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use function boolval;

/**
 * @Assert\GroupSequenceProvider()
 */
class DistributionApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice({ "rdf", "csv" })
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $slug;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $license;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $accessRights;

    /**
     * @var bool|null
     * @Assert\NotNull(groups = {"csv"})
     * @Assert\Type("bool")
     */
    private $includeAllData;

    /**
     * @var string|null
     * @Assert\NotBlank(groups = {"rdf"})
     * @Assert\Type("string")
     */
    private $dataModel;

    /**
     * @var string|null
     * @Assert\NotBlank(groups = {"rdf"})
     * @Assert\Type("string")
     */
    private $dataModelVersion;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $apiUser;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $clientId;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $clientSecret;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private $published;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
        $this->slug = $this->getFromData('slug');
        $this->license = $this->getFromData('license');
        $this->accessRights = (int) $this->getFromData('accessRights');
        $this->includeAllData = $this->getFromData('includeAllData');
        $this->dataModel = $this->getFromData('dataModel');
        $this->dataModelVersion = $this->getFromData('dataModelVersion');
        $this->apiUser = $this->getFromData('apiUser');
        $this->clientId = $this->getFromData('clientId');
        $this->clientSecret = $this->getFromData('clientSecret');
        $this->published = boolval($this->getFromData('published'));
    }

    public function getType(): DistributionType
    {
        return DistributionType::fromString($this->type);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getAccessRights(): int
    {
        return $this->accessRights;
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

    /** @inheritDoc */
    public function getGroupSequence()
    {
        return [
            'DistributionApiRequest',
            $this->getType()->toString(),
        ];
    }
}
