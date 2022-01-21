<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\FAIRData\Distribution;
use App\Security\User;
use App\Type\DistributionAccessType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function assert;
use function in_array;

class DistributionVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const ACCESS_DATA = 'access_data';
    public const MANAGE = 'manage';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports($attribute, $subject)
    {
        if (! in_array($attribute, [self::VIEW, self::EDIT, self::ACCESS_DATA, self::MANAGE], true)) {
            return false;
        }

        return $subject instanceof Distribution;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $distribution = $subject;
        assert($distribution instanceof Distribution);

        if ($attribute === self::VIEW && $distribution->isPublished()) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $permission = $distribution->getPermissionsForUser($user);

        if($attribute === self::ACCESS_DATA) {
            return $this->canAccessData($distribution, $user);
        }

        if ($permission->getType()->isView()) {
            return $attribute === self::VIEW;
        }

        if ($permission->getType()->isEdit()) {
            return $attribute === self::VIEW || $attribute === self::EDIT;
        }

        if ($permission->getType()->isManage()) {
            return $attribute === self::VIEW || $attribute === self::EDIT || $attribute === self::MANAGE;
        }

        return false;
    }

    private function canAccessData(Distribution $distribution, User $user): bool
    {
        if ($distribution->getContents() === null) {
            return false;
        }

        if ($distribution->getContents()->getAccessRights() === DistributionAccessType::PUBLIC) {
            return true;
        }

        if (! $user->hasCastorUser()) {
            return false;
        }

        return $user->getCastorUser()->hasAccessToStudy($distribution->getDataset()->getStudy()->getSourceId());
    }
}
