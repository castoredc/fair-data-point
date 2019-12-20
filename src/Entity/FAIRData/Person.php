<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;
use function array_merge;

/**
 * @ORM\Entity
 */
class Person extends Agent
{
    /**
     * @ORM\Column(type="iri")
     *
     * @var Iri|null
     */
    private $orcid;

    public function __construct(string $name, ?Iri $orcid)
    {
        $slugify = new Slugify();

        parent::__construct($slugify->slugify($name), $name);
        $this->orcid = $orcid;
    }

    public function getAccessUrl(): string
    {
        return $this->getFairDataPoint()->getIri() . '/agent/person/' . $this->getSlug();
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->orcid->getValue(),
            'orcid' => $this->orcid->getValue(),
            'type' => 'person',
        ]);
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $url = $this->getAccessUrl();
        if ($this->orcid !== null) {
            $url = $this->orcid->getValue();
        }

        $graph->addResource($url, 'a', 'foaf:Person');
        $graph->addLiteral($url, 'foaf:name', $this->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }
}
