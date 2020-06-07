<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Iri;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_prefix")
 * @ORM\HasLifecycleCallbacks
 */
class NamespacePrefix
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $prefix;

    /**
     * @ORM\Column(type="iri", nullable=false)
     *
     * @var Iri
     */
    private $uri;

    /**
     * @ORM\ManyToOne(targetEntity="DataModel", inversedBy="prefixes",cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     *
     * @var DataModel
     */
    private $dataModel;

    public function __construct(string $prefix, Iri $uri)
    {
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getUri(): Iri
    {
        return $this->uri;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setUri(Iri $uri): void
    {
        $this->uri = $uri;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->dataModel = $dataModel;
    }
}
