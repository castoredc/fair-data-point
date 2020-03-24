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
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $middleName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="iri")
     *
     * @var Iri|null
     */
    private $orcid;

    public function __construct(string $firstName, ?string $middleName, string $lastName, string $email, string $phoneNumber, ?Iri $orcid)
    {
        $slugify = new Slugify();

        $fullName = !is_null($middleName) ? $firstName . ' ' . $middleName . ' ' . $lastName : $firstName . ' ' . $lastName;
        parent::__construct($slugify->slugify($fullName), $fullName);

        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->orcid = $orcid;
    }

    public function getAccessUrl(): string
    {
        return '/agent/person/' . $this->getSlug();
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
