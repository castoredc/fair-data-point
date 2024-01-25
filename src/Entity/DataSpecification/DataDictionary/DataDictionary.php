<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\DataSpecification;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionary extends DataSpecification
{
}
