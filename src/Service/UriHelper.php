<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\FAIRDataPoint;
use Doctrine\ORM\EntityManagerInterface;

class UriHelper
{
    public const ISO_URL = 'http://id.loc.gov/vocabulary/iso639-1/';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getBaseUri(): string
    {
        /** @var FAIRDataPoint[] $fdps */
        $fdps = $this->em->getRepository(FAIRDataPoint::class)->findAll();
        $fdp = $fdps[0];

        return $fdp->getIri()->getValue();
    }

    public function getBasePurl(): string
    {
        /** @var FAIRDataPoint[] $fdps */
        $fdps = $this->em->getRepository(FAIRDataPoint::class)->findAll();
        $fdp = $fdps[0];

        if ($fdp->getPurl() !== null) {
            return $fdp->getPurl()->getValue();
        }

        return $fdp->getIri()->getValue();
    }

    public function getUri(mixed $object, bool $addTrailingSlash = false): ?string
    {
        if (! $object instanceof AccessibleEntity) {
            return null;
        }

        return $this->getBaseUri() . $object->getRelativeUrl() . ($addTrailingSlash ? '/' : '');
    }
}
