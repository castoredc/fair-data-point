<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Data\DataSpecification\DataSpecification;
use App\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use function assert;
use function in_array;

class DataSpecificationVoter extends Voter
{
    public const VIEW = 'view';
    public const ADD = 'add';
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
        if (! in_array($attribute, [self::VIEW, self::ADD, self::EDIT, self::MANAGE], true)) {
            return false;
        }

        return $subject instanceof DataSpecification;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $dataSpecification = $subject;
        assert($dataSpecification instanceof DataSpecification);

        if ($attribute === self::VIEW && $dataSpecification->isPublic()) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $permission = $dataSpecification->getPermissionsForUser($user);

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
