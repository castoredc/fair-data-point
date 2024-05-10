<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\UpdateTripleCommand as CommonUpdateTripleCommand;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Enum\NodeType;

class UpdateTripleCommand extends CommonUpdateTripleCommand
{
    public function __construct(
        private Triple $triple,
        NodeType $objectType,
        ?string $objectValue,
        ?string $predicateValue,
        NodeType $subjectType,
        ?string $subjectValue,
    ) {
        parent::__construct($objectType, $objectValue, $predicateValue, $subjectType, $subjectValue);
    }

    public function getTriple(): Triple
    {
        return $this->triple;
    }
}
