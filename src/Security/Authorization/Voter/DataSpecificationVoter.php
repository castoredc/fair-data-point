<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\DataSpecification\Common\DataSpecification;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function assert;
use function in_array;

class DataSpecificationVoter extends Voter
{
    public const USE = 'use';
    public const VIEW = 'view';
    public const ADD = 'add';
    public const EDIT = 'edit';
    public const MANAGE = 'manage';

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {
    }

    /** @inheritDoc */
    protected function supports(string $attribute, $subject): bool
    {
        if (! in_array($attribute, [self::USE, self::VIEW, self::ADD, self::EDIT, self::MANAGE], true)) {
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

        if (($attribute === self::VIEW || $attribute === self::USE) && $dataSpecification->isPublic()) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($dataSpecification instanceof MetadataModel) {
            $inherited = $this->em->getRepository(MetadataModel::class)->isInUseByEntitiesUserHasPermissionsTo($dataSpecification, $user);

            if ($inherited && $attribute === self::USE) {
                return true;
            }
        }

        $permission = $dataSpecification->getPermissionsForUser($user);

        if ($permission === null) {
            return false;
        }

        if ($permission->getType()->isView()) {
            return $attribute === self::VIEW || $attribute === self::USE;
        }

        if ($permission->getType()->isEdit()) {
            return $attribute === self::VIEW || $attribute === self::EDIT || $attribute === self::USE;
        }

        if ($permission->getType()->isManage()) {
            return $attribute === self::VIEW || $attribute === self::EDIT || $attribute === self::MANAGE || $attribute === self::USE;
        }

        return false;
    }
}
