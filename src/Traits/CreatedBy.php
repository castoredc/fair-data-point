<?php
declare(strict_types=1);

namespace App\Traits;

use App\Security\CastorUser;

trait CreatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorUser")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     *
     * @var CastorUser|null
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     */
    private $createdBy;

    public function getCreatedBy(): ?CastorUser
    {
        return $this->createdBy;
    }
}
