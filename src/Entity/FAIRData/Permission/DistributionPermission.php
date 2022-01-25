<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Distribution;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission_distribution")
 */
class DistributionPermission extends Permission
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="distributions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected User $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Distribution", cascade={"persist"}, fetch = "EAGER", inversedBy="permissions")
     * @ORM\JoinColumn(name="distribution_id", referencedColumnName="id")
     */
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
