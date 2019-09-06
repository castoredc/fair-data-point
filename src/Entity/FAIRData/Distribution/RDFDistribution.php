<?php


namespace App\Entity\FAIRData\Distribution;


use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends Distribution
{
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $twig;

    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, array $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, string $twig)
    {
        parent::__construct($slug, $title, $version, $description, $publishers, $language, $license, $issued, $modified);

        $this->twig = $twig;
    }

    /**
     * @return string
     */
    public function getTwig(): string
    {
        return $this->twig;
    }

    /**
     * @param string $twig
     */
    public function setTwig(string $twig): void
    {
        $this->twig = $twig;
    }

    public function getRDFUrl()
    {
        return parent::getAccessUrl() . '/rdf';
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'rdf_url' => $this->getRDFUrl()
        ]);
    }

}