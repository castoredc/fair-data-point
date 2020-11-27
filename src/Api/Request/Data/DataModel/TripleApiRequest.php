<?php
declare(strict_types=1);

namespace App\Api\Request\Data\DataModel;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\NodeType;
use Symfony\Component\Validator\Constraints as Assert;

class TripleApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $objectType;

    /** @Assert\Type("string") */
    private ?string $objectValue = null;

    /** @Assert\Type("string") */
    private ?string $predicateValue = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $subjectType;

    /** @Assert\Type("string") */
    private ?string $subjectValue = null;

    protected function parse(): void
    {
        $this->objectType = $this->getFromData('objectType');
        $this->objectValue = $this->getFromData('objectValue');
        $this->predicateValue = $this->getFromData('predicateValue');
        $this->subjectType = $this->getFromData('subjectType');
        $this->subjectValue = $this->getFromData('subjectValue');
    }

    public function getObjectType(): NodeType
    {
        return NodeType::fromString($this->objectType);
    }

    public function getObjectValue(): ?string
    {
        return $this->objectValue;
    }

    public function getPredicateValue(): ?string
    {
        return $this->predicateValue;
    }

    public function getSubjectType(): NodeType
    {
        return NodeType::fromString($this->subjectType);
    }

    public function getSubjectValue(): ?string
    {
        return $this->subjectValue;
    }
}
