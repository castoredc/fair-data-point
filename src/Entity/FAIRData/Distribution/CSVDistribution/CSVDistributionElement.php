<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\CSVDistribution;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="distribution_csv_elements")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"field_id" = "CSVDistributionElementFieldId", "variable_name" = "CSVDistributionElementVariableName"})
 */
abstract class CSVDistributionElement
{
    public const FIELD_ID = 'fieldId';
    public const VARIABLE_NAME = 'variableName';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CSVDistribution", inversedBy="elements",cascade={"persist"})
     *
     * @var CSVDistribution|null
     */

    private $distribution;

    public function getId(): string
    {
        return $this->id;
    }

    public function getDistribution(): ?CSVDistribution
    {
        return $this->distribution;
    }

    public function setDistribution(?CSVDistribution $distribution): void
    {
        $this->distribution = $distribution;
    }
}
