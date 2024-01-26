<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\DataSpecification\Common\DataSpecificationModuleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class MetadataModelModuleApiRequest extends DataSpecificationModuleApiRequest
{
    public function getGroupSequence(): array|Assert\GroupSequence
    {
        return ['MetadataModelModuleApiRequest'];
    }
}
