<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\DataSpecification;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_dictionary')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DataDictionary extends DataSpecification
{
}
