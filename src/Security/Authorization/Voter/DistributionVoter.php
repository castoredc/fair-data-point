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

        return $subject instanceof Distribution;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $distribution = $subject;
        assert($distribution instanceof Distribution);

        switch ($attribute) {
            case self::VIEW:
                return $this->security->isGranted(self::VIEW, $distribution->getDataset());
            case self::EDIT:
                return $this->security->isGranted(self::EDIT, $distribution->getDataset());
            case self::ACCESS_DATA:
                return $this->canAccessData($distribution, $token);
        }

        return false;
    }

    private function canAccessData(Distribution $distribution, TokenInterface $token): bool
    {
        if ($distribution->getContents() === null) {
            return false;
        }

        if ($distribution->getContents()->getAccessRights() === DistributionAccessType::PUBLIC) {
            return true;
        }

        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        if (! $user->hasCastorUser()) {
            return false;
        }

        return $user->getCastorUser()->hasAccessToStudy($distribution->getDataset()->getStudy()->getSourceId());
    }
}
