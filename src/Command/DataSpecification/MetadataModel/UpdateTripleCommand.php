<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateTripleCommand as CommonUpdateTripleCommand;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\Enum\NodeType;

class UpdateTripleCommand extends CommonUpdateTripleCommand
{
    private Triple $triple;

    public function __construct(
        Triple $triple,
        NodeType $objectType,
        ?string $objectValue,
        ?string $predicateValue,
        NodeType $subjectType,
        ?string $subjectValue
    ) {
        parent::__construct($objectType, $objectValue, $predicateValue, $subjectType, $subjectValue);

        $this->triple = $triple;
    }

    public function getTriple(): Triple
    {
        return $this->triple;
    }
}
