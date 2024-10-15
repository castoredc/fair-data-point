<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Entity\Enum\PermissionType;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'permission_data_specification')]
#[ORM\Entity]
class DataSpecificationPermission extends Permission
{
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Security\User::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'dataSpecifications')]
    protected User $user;

    #[ORM\JoinColumn(name: 'data_specification_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \DataSpecification::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'permissions')]
    private DataSpecification $dataSpecification;

    public function __construct(User $user, PermissionType $type, DataSpecification $dataSpecification)
    {
        parent::__construct($user, $type);

        $this->dataSpecification = $dataSpecification;
    }

    public function getEntity(): DataSpecification
    {
        return $this->dataSpecification;
    }
}
