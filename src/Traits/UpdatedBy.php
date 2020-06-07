<?php
declare(strict_types=1);

namespace App\Traits;

use App\Security\CastorUser;

trait UpdatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorUser")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     *
     * @var CastorUser|null
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     */
    private $updatedBy;

    public function getUpdatedBy(): CastorUser
    {
        return $this->updatedBy;
    }
}
