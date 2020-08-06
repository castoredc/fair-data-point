<?php
declare(strict_types=1);

namespace App\Traits;

use App\Security\User;

trait UpdatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Security\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     *
     * @var User|null
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     */
    private $updatedBy;

    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }
}
