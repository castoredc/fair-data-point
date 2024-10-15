<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\OptionGroupOption;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model_option_group_option')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MetadataModelOptionGroupOption extends OptionGroupOption
{
    /** @param array<mixed> $data */
    public static function fromData(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'] ?? null,
            $data['value'],
            $data['order'] ?? null
        );
    }
}
