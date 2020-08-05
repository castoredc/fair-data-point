<?php
declare(strict_types=1);

namespace App\Message\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\CastorEntityType;

class GetCastorEntityCommand
{
    /** @var CastorStudy */
    private $study;

    /** @var CastorEntityType */
    private $type;

    /** @var string */
    private $id;

    /** @var string|null */
    private $parentId;

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
