<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\FAIRData\Catalog;
use App\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function assert;
use function in_array;

class CatalogVoter extends Voter
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

        return $subject instanceof Catalog;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $catalog = $subject;
        assert($catalog instanceof Catalog);

        switch ($attribute) {
            case self::VIEW:
                return true;
            case self::ADD:
                return $this->canAdd($catalog, $token);
            case self::EDIT:
                return false;
        }

        return false;
    }

    private function canAdd(Catalog $catalog, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        if (! $user->hasCastorUser()) {
            return false;
        }

        return $catalog->isAcceptingSubmissions();
    }
}
