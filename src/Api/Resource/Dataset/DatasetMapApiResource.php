<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\FAIRData\Organization;
use Doctrine\Common\Collections\ArrayCollection;

class DatasetMapApiResource implements ApiResource
{
    /** @var Dataset */
    private $dataset;

    /** @var Organization */
    private $organization;

    public function __construct(Dataset $dataset, Organization $organization)
    {
        $this->dataset = $dataset;
        $this->organization = $organization;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $study = $this->dataset->getStudy();
        $metadata = $study->getLatestMetadata();

        $title = new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getBriefName(), $this->dataset->getLanguage())]));

        $coordinates = [
            'lat' => $this->organization->getCoordinatesLatitude(),
            'long' => $this->organization->getCoordinatesLongitude(),
        ];

        return [
            'title' => $title->toArray(),
            'relative_url' => $this->dataset->getRelativeUrl(),
            'organization' => $this->organization->getName(),
            'city' => $this->organization->getCity(),
            'country' => $this->organization->getCountry()->getName(),
            'coordinates' => $coordinates,
        ];
    }
}
