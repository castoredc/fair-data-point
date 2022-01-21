<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission_data_specification")
 */
class DataSpecificationPermission extends Permission
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="dataSpecifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected User $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="DataSpecification", cascade={"persist"}, fetch = "EAGER", inversedBy="permissions")
     * @ORM\JoinColumn(name="data_specification_id", referencedColumnName="id")
     */
    private DataSpecification $dataSpecification;

    public function getEntity(): DataSpecification
    {
        return $this->dataSpecification;
    }
}
