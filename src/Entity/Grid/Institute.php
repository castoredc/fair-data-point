<?php
declare(strict_types=1);

namespace App\Entity\Grid;

use App\Entity\Iri;
use function count;

class Institute
{
    private string $id;

    private string $name;

    /** @var Address[] */
    private array $addresses = [];

    /** @var Iri[] */
    private array $links = [];

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return Address[] */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): void
    {
        $this->addresses[] = $address;
    }

    public function hasLinks(): bool
    {
        return count($this->links) > 0;
    }

    /** @return Iri[] */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function addLink(Iri $link): void
    {
        $this->links[] = $link;
    }

    public function getMainAddress(): Address
    {
        foreach ($this->addresses as $address) {
            if ($address->isPrimary()) {
                return $address;
            }
        }

        return $this->addresses[0];
    }
}
