<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\FAIRData\Catalog;
use App\Security\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function assert;
use function in_array;

class CatalogVoter extends Voter
{
    public const VIEW = 'view';
    public const ADD = 'add';
    public const EDIT = 'edit';
    public const MANAGE = 'manage';

    public function __construct(private Security $security)
    {
    }

    /** @inheritDoc */
    protected function supports(string $attribute, $subject): bool
    {
        if (! in_array($attribute, [self::VIEW, self::ADD, self::EDIT, self::MANAGE], true)) {
            return false;
        }

        return $subject instanceof Catalog;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        $catalog = $subject;
        assert($catalog instanceof Catalog);

        if ($attribute === self::VIEW && $catalog->isPublic()) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $permission = $catalog->getPermissionsForUser($user);

        if (! $user->hasCastorUser()) {
            return false;
        }

        if ($permission !== null) {
            if ($permission->getType()->isView()) {
                if ($catalog->isAcceptingSubmissions()) {
                    return $attribute === self::VIEW || $attribute === self::ADD;
                }

                return $attribute === self::VIEW;
            }

            if ($permission->getType()->isEdit()) {
                return $attribute === self::VIEW || $attribute === self::EDIT || $attribute === self::ADD;
            }

            if ($permission->getType()->isManage()) {
                return $attribute === self::VIEW || $attribute === self::EDIT || $attribute === self::ADD || $attribute === self::MANAGE;
            }
        }

        return false;
    }
}
