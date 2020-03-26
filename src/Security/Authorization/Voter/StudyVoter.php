<?php

namespace App\Security\Authorization\Voter;

use App\Entity\Castor\Study;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

class StudyVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Study) {
            return false;
        }

        return true;
    }

    /**
     * @param                $attribute
     * @param                $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof CastorUser) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Study $study */
        $study = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($study, $user);
            case self::EDIT:
                return $this->canEdit($study, $user);
        }
    }


    private function canView(Study $study, CastorUser $user)
    {
        if($this->canEdit($study, $user))
        {
            return true;
        }

        return (! is_null($study->getDataset()));
    }

    private function canEdit(Study $study, CastorUser $user)
    {
        return in_array($study->getId(), $user->getStudies());
    }
}