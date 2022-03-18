<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="text_coded")
 */
class CodedText
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $text;

    /** @var OntologyConcept[]|ArrayCollection */
    private $concepts;

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->concepts = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /** @return OntologyConcept[]|ArrayCollection */
    public function getConcepts()
    {
        return $this->concepts;
    }

    /** @return array<string> */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }
}
