<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Api\Resource\ApiResource;
use App\Security\User;
use function in_array;

class UserApiResource implements ApiResource
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->user->getId(),
            'details' => $this->user->getPerson() !== null ? (new PersonApiResource($this->user->getPerson()))->toArray() : null,
            'isAdmin' => in_array('ROLE_ADMIN', $this->user->getRoles(), true),
            'linkedAccounts' => [
                'castor' => $this->user->hasCastorUser() ? $this->user->getCastorUser()->toArray() : false,
                'orcid' => $this->user->hasOrcid() ? $this->user->getOrcid()->toArray() : false,
            ],
        ];
    }
}
