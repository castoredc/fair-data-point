<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Castor\Study;
use App\Security\CastorUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function in_array;

class StudyVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports($attribute, $subject)
    {
        if (! in_array($attribute, [self::VIEW, self::EDIT], true)) {
            return false;
        }

        return $subject instanceof Study;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof CastorUser) {
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

        return false;
    }

    private function canView(Study $study, CastorUser $user): bool
    {
        if ($this->canEdit($study, $user)) {
            return true;
        }

        return $study->getDataset() !== null;
    }

    private function canEdit(Study $study, CastorUser $user): bool
    {
        return in_array($study->getId(), $user->getStudies(), true);
    }
}
