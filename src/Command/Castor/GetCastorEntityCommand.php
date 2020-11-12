<?php
declare(strict_types=1);

namespace App\Command\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\CastorEntityType;

class GetCastorEntityCommand
{
    private CastorStudy $study;

    private CastorEntityType $type;

    private string $id;

    private ?string $parentId = null;

    public function __construct(CastorStudy $study, CastorEntityType $type, string $id, ?string $parentId)
    {
        $this->study = $study;
        $this->type = $type;
        $this->id = $id;
        $this->parentId = $parentId;
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
