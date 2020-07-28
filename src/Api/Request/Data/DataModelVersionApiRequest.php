<?php
declare(strict_types=1);

namespace App\Api\Request\Data;

use App\Api\Request\SingleFormApiRequest;
use App\Entity\Version;
use Symfony\Component\Validator\Constraints as Assert;

class DataModelVersionApiRequest extends SingleFormApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $version;

    protected function parse(): void
    {
        $this->version = $this->getFromQuery('version');
    }

    public function getVersion(): Version
    {
        return new Version($this->version);
    }
}
