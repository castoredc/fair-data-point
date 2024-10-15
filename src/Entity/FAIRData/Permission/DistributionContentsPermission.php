<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Enum\PermissionType;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'permission_distribution_contents')]
#[ORM\Entity]
class DistributionContentsPermission extends Permission
{
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Security\User::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'distributions')]
    protected User $user;

    #[ORM\JoinColumn(name: 'distribution_contents_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Data\DistributionContents\DistributionContents::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'permissions')]
    private DistributionContents $distributionContents;

    public function __construct(User $user, PermissionType $type, DistributionContents $distributionContents)
    {
        parent::__construct($user, $type);

        $this->distributionContents = $distributionContents;
    }

    public function getEntity(): DistributionContents
    {
        return $this->distributionContents;
    }
}
