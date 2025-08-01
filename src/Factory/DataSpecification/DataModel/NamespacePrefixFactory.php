<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\NamespacePrefix;
use App\Entity\Iri;

class NamespacePrefixFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(array $data): NamespacePrefix
    {
        return new NamespacePrefix(
            $data['prefix'],
            new Iri($data['uri'])
        );
    }
}
