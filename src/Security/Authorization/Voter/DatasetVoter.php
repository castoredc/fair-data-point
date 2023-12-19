<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\FAIRData\Dataset;
use App\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function assert;
use function in_array;

class DatasetVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const MANAGE = 'manage';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports(string $attribute, $subject): bool
    {
        if (! in_array($attribute, [self::VIEW, self::EDIT, self::MANAGE], true)) {
            return false;
        }

        return $subject instanceof Dataset;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $dataset = $subject;
        assert($dataset instanceof Dataset);

        if ($attribute === self::VIEW && $dataset->isPublished()) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $permission = $dataset->getPermissionsForUser($user);

        if ($permission === null) {
            return false;
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
}
