<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Study;
use App\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function assert;
use function in_array;

class StudyVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const ACCESS_DATA = 'access_data';
    private Security $security;

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
        $study = $subject;
        assert($study instanceof Study);

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

        if (! $user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (! $user->hasCastorUser()) {
            return false;
        }

        return $user->getCastorUser()->hasAccessToStudy($study->getSourceId());
    }
}
