<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\DataSpecification;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModel extends DataSpecification
{
}
