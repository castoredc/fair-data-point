<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\OptionGroup;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_option_group")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModelOptionGroup extends OptionGroup
{
    public function getMetadataModelVersion(): MetadataModelVersion
    {
        $version = $this->getVersion();
        assert($version instanceof MetadataModelVersion);

        return $version;
    }
}
