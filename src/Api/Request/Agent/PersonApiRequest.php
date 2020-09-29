<?php
declare(strict_types=1);

namespace App\Api\Request\Agent;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class PersonApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email = null;

    protected function parse(): void
    {
        $this->email = $this->getFromQuery('email');
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
