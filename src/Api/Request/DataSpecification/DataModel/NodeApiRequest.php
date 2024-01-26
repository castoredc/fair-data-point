<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\DataModel;

use App\Api\Request\DataSpecification\Common\Model\NodeApiRequest as CommonNodeApiRequest;
use function boolval;

class NodeApiRequest extends CommonNodeApiRequest
{
    /** @Assert\Type("bool") */
    private bool $repeated;

    protected function parse(): void
    {
        parent::parse();

        $this->repeated = boolval($this->getFromData('repeated'));
    }

    public function isRepeated(): bool
    {
        return $this->repeated;
    }
}
