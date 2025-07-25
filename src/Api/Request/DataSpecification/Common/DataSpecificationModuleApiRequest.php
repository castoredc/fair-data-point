<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\Common;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

abstract class DataSpecificationModuleApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $title;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    private int $order;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}
