<?php
declare(strict_types=1);

namespace App\Api\Request\Agent;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class OrganizationApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $country;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $search;

    protected function parse(): void
    {
        $this->country = $this->getFromQuery('country');
        $this->search = $this->getFromQuery('search');
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getSearch(): string
    {
        return $this->search;
    }
}
