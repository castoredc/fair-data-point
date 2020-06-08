<?php
declare(strict_types=1);

namespace App\Api\Request\Data;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\NodeType;
use Symfony\Component\Validator\Constraints as Assert;

class TripleApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $objectType;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $objectValue;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $predicateValue;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $subjectType;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $subjectValue;

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
