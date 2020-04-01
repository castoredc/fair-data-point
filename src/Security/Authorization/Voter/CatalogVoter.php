<?php
declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\FAIRData\Catalog;
use App\Security\CastorUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function in_array;

class CatalogVoter extends Voter
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

        return $subject instanceof Catalog;
    }

    /** @inheritDoc */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Catalog $catalog */
        $catalog = $subject;

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
        if (! $token->getUser() instanceof CastorUser) {
            return false;
        }

        return $catalog->isAcceptingSubmissions();
    }
}
