<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Entity\Enum\VersionType;

abstract class CreateModelVersionCommand
{
    private VersionType $versionType;

    public function __construct(VersionType $versionType)
    {
        $this->versionType = $versionType;
    }

    public function getVersionType(): VersionType
    {
        return $this->versionType;
    }
}
