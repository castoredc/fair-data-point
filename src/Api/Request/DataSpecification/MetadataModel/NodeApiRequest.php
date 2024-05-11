<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\DataSpecification\Common\Model\NodeApiRequest as CommonNodeApiRequest;
use App\Entity\Enum\ResourceType;
use Symfony\Component\Validator\Constraints as Assert;

class NodeApiRequest extends CommonNodeApiRequest
{
    /** @Assert\Type("string") */
    private ?string $useAsTitle = null;

    protected function parse(): void
    {
        parent::parse();

        $this->useAsTitle = $this->getFromData('useAsTitle');
    }

    public function getUseAsTitle(): ?ResourceType
    {
        return $this->useAsTitle !== null ? ResourceType::fromString($this->useAsTitle) : null;
    }
}
