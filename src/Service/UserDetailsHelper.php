<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\Wizard;
use App\Security\User;
use function preg_replace;
use function strpos;
use function trim;

class UserDetailsHelper
{
    private User $user;
    private array $wizards;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getWizards(): array
    {
        $wizards = [];

        foreach ($this->user->getWizards() as $wizard) {
            $wizards[$wizard->toString()] = true;
        }

        return $wizards;
    }

    private function hasWizard(Wizard $wizard): bool
    {
        foreach ($this->user->getWizards() as $userWizard) {
            if ($userWizard->isEqualTo($wizard)) {
                return true;
            }
        }

        return false;
    }

    public function shouldShowDetailsSuggestions(): bool
    {
        return $this->hasWizard(Wizard::details());
    }

    public function shouldShowAffiliationsSuggestions(): bool
    {
        return $this->hasWizard(Wizard::affiliations());
    }

    /** @return mixed[] */
    public function getDetailsSuggestions(): array
    {
        $suggestions = [
            'firstName' => '',
            'lastName' => '',
        ];

        if ($this->user->hasOrcid()) {
            $name = trim($this->user->getOrcid()->getName());
            $suggestions['lastName'] = strpos($name, ' ') === false ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $suggestions['firstName'] = trim(preg_replace('#' . $suggestions['lastName'] . '#', '', $name));
        }

        return $suggestions;
    }
}
