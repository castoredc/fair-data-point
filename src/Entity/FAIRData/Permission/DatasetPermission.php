<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'permission_dataset')]
#[ORM\Entity]
class DatasetPermission extends Permission
{
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Security\User::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'datasets')]
    protected User $user;

    #[ORM\JoinColumn(name: 'dataset_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\Dataset::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'permissions')]
    private Dataset $dataset;

    public function __construct(User $user, PermissionType $type, Dataset $dataset)
    {
        parent::__construct($user, $type);

        $this->dataset = $dataset;
    }

    public function getEntity(): Dataset
    {
        return $this->dataset;
    }
}
