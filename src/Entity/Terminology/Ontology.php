<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;

class Ontology
{
    /** @var string */
    private $name;

    /** @var Iri */
    private $url;

    /** @var string */
    private $bioPortalId;

    public function __construct(string $name, Iri $url, string $bioPortalId)
    {
        $this->name = $name;
        $this->url = $url;
        $this->bioPortalId = $bioPortalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function setUrl(Iri $url): void
    {
        $this->url = $url;
    }

    public function getBioPortalId(): string
    {
        return $this->bioPortalId;
    }

    public function setBioPortalId(string $bioPortalId): void
    {
        $this->bioPortalId = $bioPortalId;
    }
}
