<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Study;
use App\Security\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function assert;
use function in_array;

class StudyVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const EDIT_SOURCE_SYSTEM = 'edit_source_system';

    public function __construct(private Security $security)
    {
    }

    /** @inheritDoc */
    protected function supports(string $attribute, $subject): bool
    {
        if (! in_array($attribute, [self::VIEW, self::EDIT, self::EDIT_SOURCE_SYSTEM], true)) {
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

            case self::EDIT_SOURCE_SYSTEM:
                return $this->canEditInSourceSystem($study, $token);
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

        return $this->canEditInSourceSystem($study, $token);
    }

    private function canEditInSourceSystem(Study $study, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        if (! $user->hasCastorUser()) {
            return false;
        }

        if ($study->getSource()->isCastor()) {
            return $study->getSourceId() !== null ? $user->getCastorUser()->hasAccessToStudy($study->getSourceId()) : false;
        }

        return false;
    }
}
