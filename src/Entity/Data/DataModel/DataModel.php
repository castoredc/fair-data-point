<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Data\DataSpecification\DataSpecification;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model")
 * @ORM\HasLifecycleCallbacks
 */
class DataModel extends DataSpecification
{
}
