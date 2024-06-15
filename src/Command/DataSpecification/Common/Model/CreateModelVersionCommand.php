<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Entity\Enum\VersionType;

abstract class CreateModelVersionCommand
{
    public function __construct(private VersionType $versionType)
    {
    }

    public function getVersionType(): VersionType
    {
        return $this->versionType;
    }
}
