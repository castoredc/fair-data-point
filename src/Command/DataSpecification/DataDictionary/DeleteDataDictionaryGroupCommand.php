<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;

class DeleteDataDictionaryGroupCommand
{
    private DataDictionaryGroup $group;

    public function __construct(DataDictionaryGroup $group)
    {
        $this->group = $group;
    }

    public function getGroup(): DataDictionaryGroup
    {
        return $this->group;
    }
}
