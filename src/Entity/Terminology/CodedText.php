<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'text_coded')]
#[ORM\Entity]
class CodedText
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string')]
    private string $text;

    /** @var OntologyConcept[]|ArrayCollection<OntologyConcept> */
    private array|ArrayCollection $concepts;

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->concepts = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /** @return OntologyConcept[]|ArrayCollection<OntologyConcept> */
    public function getConcepts(): array|ArrayCollection
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
