<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Study;
use App\Security\CastorUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function in_array;

class StudyVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const ACCESS_DATA = 'access_data';

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports($attribute, $subject)
    {
        if (! in_array($attribute, [self::VIEW, self::EDIT, self::ACCESS_DATA], true)) {
            return false;
        }

        return $subject instanceof Study;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // you know $subject is a Post object, thanks to `supports()`
        /** @var Study $study */
        $study = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($study, $token);
            case self::EDIT:
                return $this->canEdit($study, $token);
        }

        return false;
    }

    private function canView(Study $study, TokenInterface $token): bool
    {
        if ($this->canEdit($study, $token)) {
            return true;
        }

        return $study->isPublished();
    }

    private function canEdit(Study $study, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (! $user instanceof CastorUser) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return in_array($study->getSourceId(), $user->getStudies(), true);
    }
}
