<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Data\DataModel\DataModel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function in_array;

class DataModelVoter extends Voter
{
    public const VIEW = 'view';
    public const ADD = 'add';
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
        if (! in_array($attribute, [self::VIEW, self::ADD, self::EDIT], true)) {
            return false;
        }

        return $subject instanceof DataModel;
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

        /** @var DataModel $dataModel */
        $dataModel = $subject;
        $distributions = $dataModel->getDistributions();

        foreach ($distributions as $distribution) {
            if ($this->security->isGranted('view', $distribution)) {
                return true;
            }
        }

        return false;
    }
}
