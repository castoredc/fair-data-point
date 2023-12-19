<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use function assert;
use function in_array;

class DistributionContentsVoter extends Voter
{
    public const ACCESS_DATA = 'access_data';
    public const MANAGE = 'manage';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports(string $attribute, $subject): bool
    {
        if (! in_array($attribute, [self::ACCESS_DATA, self::MANAGE], true)) {
            return false;
        }

        return $subject instanceof DistributionContents;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $contents = $subject;
        assert($contents instanceof DistributionContents);
        $distribution = $contents->getDistribution();

        if ($attribute === self::MANAGE) {
            return $this->security->isGranted(DistributionVoter::MANAGE, $distribution);
        }

        if (
            $this->security->isGranted('ROLE_ADMIN') ||
            $contents->isPublic()
        ) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        $permission = $distribution->getPermissionsForUser($user);

        if (
            $permission === null
            || $distribution->getDataset()->getStudy()->getSourceId() === null
            || ! $user->hasCastorUser()
        ) {
            return false;
        }

        if ($permission->getType()->isAccessData()) {
            return $attribute === self::ACCESS_DATA;
        }

        return $user->getCastorUser()->hasAccessToStudy($distribution->getDataset()->getStudy()->getSourceId());
    }
}
