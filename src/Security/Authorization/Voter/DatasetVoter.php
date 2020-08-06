<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\FAIRData\Dataset;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function in_array;

class DatasetVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @inheritDoc */
    protected function supports($attribute, $subject)
    {
        if (! in_array($attribute, [self::VIEW, self::EDIT], true)) {
            return false;
        }

        return $subject instanceof Dataset;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Dataset $dataset */
        $dataset = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->security->isGranted(self::VIEW, $dataset->getStudy());
            case self::EDIT:
                return $this->security->isGranted(self::EDIT, $dataset->getStudy());
        }

        return false;
    }
}
