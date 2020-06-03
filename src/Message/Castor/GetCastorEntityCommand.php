<?php
declare(strict_types=1);

namespace App\Message\Castor;

use App\Entity\Castor\Study;
use App\Entity\Enum\CastorEntityType;
use App\Security\CastorUser;

class GetCastorEntityCommand
{
    /** @var CastorUser */
    private $user;

    /** @var Study */
    private $study;

    /** @var CastorEntityType */
    private $type;

    /** @var string */
    private $id;

    /** @var string|null */
    private $parentId;

    public function __construct(Study $study, CastorUser $user, CastorEntityType $type, string $id, ?string $parentId)
    {
        $this->user = $user;
        $this->study = $study;
        $this->type = $type;
        $this->id = $id;
        $this->parentId = $parentId;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }

    public function getStudy(): Study
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
