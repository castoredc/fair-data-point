<?php
declare(strict_types=1);

namespace App\Traits;

use App\Security\User;

trait CreatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Security\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     *
     * @var User|null
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     */
    private $createdBy;

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}
