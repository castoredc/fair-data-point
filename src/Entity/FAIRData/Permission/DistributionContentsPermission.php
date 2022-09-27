<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Enum\PermissionType;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission_distribution_contents")
 */
class DistributionContentsPermission extends Permission
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="distributions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected User $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DistributionContents\DistributionContents", cascade={"persist"}, fetch = "EAGER", inversedBy="permissions")
     * @ORM\JoinColumn(name="distribution_contents_id", referencedColumnName="id")
     */
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
