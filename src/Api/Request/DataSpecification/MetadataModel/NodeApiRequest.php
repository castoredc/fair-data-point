<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\DataSpecification\Common\Model\NodeApiRequest as CommonNodeApiRequest;
use App\Entity\Enum\MetadataFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class NodeApiRequest extends CommonNodeApiRequest
{
    /** @Assert\Type("string") */
    private ?string $fieldType = null;

    /** @Assert\Type("string") */
    private ?string $optionGroup = null;

    protected function parse(): void
    {
        parent::parse();
        $this->fieldType = $this->getFromData('fieldType');
        $this->optionGroup = $this->getFromData('optionGroup');
    }

    public function getFieldType(): ?MetadataFieldType
    {
        return $this->fieldType !== null ? MetadataFieldType::fromString($this->fieldType) : null;
    }

    public function getOptionGroup(): ?string
    {
        return $this->optionGroup;
    }
}
