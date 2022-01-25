<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission_dataset")
 */
class DatasetPermission extends Permission
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="datasets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected User $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Dataset", cascade={"persist"}, fetch = "EAGER", inversedBy="permissions")
     * @ORM\JoinColumn(name="dataset_id", referencedColumnName="id")
     */
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
