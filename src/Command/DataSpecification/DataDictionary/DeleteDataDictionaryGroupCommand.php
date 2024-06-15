<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;

class DeleteDataDictionaryGroupCommand
{
    public function __construct(private DataDictionaryGroup $group)
    {
    }

    public function getGroup(): DataDictionaryGroup
    {
        return $this->group;
    }
}
