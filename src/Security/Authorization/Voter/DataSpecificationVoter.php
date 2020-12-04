<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Data\DataSpecification\DataSpecification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function assert;
use function in_array;

class DataSpecificationVoter extends Voter
{
    public const VIEW = 'view';
    public const ADD = 'add';
    public const EDIT = 'edit';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports($attribute, $subject)
    {
        if (! in_array($attribute, [self::VIEW, self::ADD, self::EDIT], true)) {
            return false;
        }

        return $subject instanceof DataSpecification;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($attribute !== self::VIEW) {
            return false;
        }

        $dataSpecification = $subject;
        assert($dataSpecification instanceof DataSpecification);
        $versions = $dataSpecification->getVersions();

        foreach ($versions as $version) {
            foreach ($version->getDistributionContents() as $distributionContent) {
                $distribution = $distributionContent->getDistribution();

                if ($this->security->isGranted('view', $distribution)) {
                    return true;
                }
            }
        }

        return false;
    }
}
