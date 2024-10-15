<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\DataSpecification;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_model')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DataModel extends DataSpecification
{
}
