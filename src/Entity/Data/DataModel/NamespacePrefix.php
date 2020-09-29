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
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $prefix;

    /** @ORM\Column(type="iri", nullable=false) */
    private Iri $uri;

    /**
     * @ORM\ManyToOne(targetEntity="DataModelVersion", inversedBy="prefixes",cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $dataModel;

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

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModelVersion $dataModel): void
    {
        $this->dataModel = $dataModel;
    }
}
