<?php
declare(strict_types=1);

namespace App\Command\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\CastorEntityType;

class GetCastorEntityCommand
{
    public function __construct(private CastorStudy $study, private CastorEntityType $type, private string $id, private ?string $parentId = null)
    {
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getType(): CastorEntityType
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }
}
