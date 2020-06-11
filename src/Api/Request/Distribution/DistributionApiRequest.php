<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DistributionApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
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
     * @Assert\Type("bool")
     */
    private $includeAllData;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
        $this->slug = $this->getFromData('slug');
        $this->license = $this->getFromData('license');
        $this->accessRights = (int) $this->getFromData('accessRights');
        $this->includeAllData = $this->getFromData('includeAllData');
    }

    public function getType(): string
    {
        return $this->type;
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
}
