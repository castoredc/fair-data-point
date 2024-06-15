<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\Enum\VersionType;

class CreateDataDictionaryVersionCommand
{
    public function __construct(private DataDictionary $dataDictionary, private VersionType $versionType)
    {
    }

    public function getDataDictionary(): DataDictionary
    {
        return $this->dataDictionary;
    }

    public function getVersionType(): VersionType
    {
        return $this->versionType;
    }
}
