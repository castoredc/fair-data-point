<?php
declare(strict_types=1);

namespace App\Api\Resource\Security;

use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Api\Resource\ApiResource;
use App\Security\User;
use App\Service\UserDetailsHelper;

class UserApiResource implements ApiResource
{
    private User $user;
    private UserDetailsHelper $helper;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->helper = new UserDetailsHelper($user);
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->user->getId(),
            'details' => $this->user->getPerson() !== null ? (new PersonApiResource($this->user->getPerson()))->toArray() : null,
            'isAdmin' => $this->user->isAdmin(),
            'linkedAccounts' => [
                'castor' => $this->user->hasCastorUser() ? $this->user->getCastorUser()->toArray() : false,
                'orcid' => $this->user->hasOrcid() ? $this->user->getOrcid()->toArray() : false,
            ],
            'wizards' => $this->helper->getWizards(),
            'suggestions' => [],
        ];

        if ($this->helper->shouldShowDetailsSuggestions()) {
            $data['suggestions']['details'] = $this->helper->getDetailsSuggestions();
        }

        if ($this->helper->shouldShowAffiliationsSuggestions()) {
            $data['suggestions']['affiliations'] = [];
        }

        return $data;
    }
}
