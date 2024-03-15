<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\Common;

use App\Api\Request\SingleFormApiRequest;
use App\Entity\Version;
use Symfony\Component\Validator\Constraints as Assert;

class DataSpecificationVersionApiRequest extends SingleFormApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $version;

    protected function parse(): void
    {
        $this->version = $this->getFromQuery('version');
    }

    public function getVersion(): Version
    {
        return new Version($this->version);
    }
}
