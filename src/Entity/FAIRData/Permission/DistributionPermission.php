<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Distribution;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'permission_distribution')]
#[ORM\Entity]
class DistributionPermission extends Permission
{
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'distributions')]
    protected User $user;

    #[ORM\JoinColumn(name: 'distribution_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Distribution::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'permissions')]
    private Distribution $distribution;

    public function __construct(User $user, PermissionType $type, Distribution $distribution)
    {
        parent::__construct($user, $type);

        $this->distribution = $distribution;
    }

    public function getEntity(): Distribution
    {
        return $this->distribution;
    }
}
