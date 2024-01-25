<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\Version;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_version")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryVersion extends Version
{
    public function getDataDictionary(): DataDictionary
    {
        assert($this->dataSpecification instanceof DataDictionary);

        return $this->dataSpecification;
    }
}
