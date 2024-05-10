<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Distribution;
use App\Security\Authorization\Voter\DistributionVoter;
use function array_merge;
use function array_values;

class DistributionTreeApiResource implements ApiResource
{
    /** @param Distribution[] $distributions */
    public function __construct(private array $distributions)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $datasets = [];

        $resources = [
            'distributions' => [],
            'datasets' => [],
            'catalogs' => [],
        ];

        foreach ($this->distributions as $distribution) {
            $dataset = $distribution->getDataset();
            $datasets[$dataset->getId()] = $dataset;

            $resources['distributions'][$dataset->getId()][$distribution->getId()] = [
                'relativeUrl' => $distribution->getRelativeUrl(),
                'id' => $distribution->getId(),
                'slug' => $distribution->getSlug(),
                'title' => $distribution->getLatestMetadata()->getTitle()->toArray(),
                'type' => $distribution->getContents()->getType(),
                'permissions' => [DistributionVoter::ACCESS_DATA],
            ];
        }

        foreach ($datasets as $dataset) {
            $resources['datasets'][$dataset->getId()] = [
                'relativeUrl' => $dataset->getRelativeUrl(),
                'id' => $dataset->getId(),
                'slug' => $dataset->getSlug(),
                'title' => $dataset->getLatestMetadata()->getTitle()->toArray(),
                'distributions' => array_values($resources['distributions'][$dataset->getId()]),
            ];

            /** @var Catalog[] $catalogs */
            $catalogs = array_merge($dataset->getStudy()->getCatalogs()->toArray(), $dataset->getCatalogs()->toArray());

            foreach ($catalogs as $catalog) {
                if (! isset($resources['catalogs'][$catalog->getId()])) {
                    $resources['catalogs'][$catalog->getId()] = [
                        'relativeUrl' => $catalog->getRelativeUrl(),
                        'id' => $catalog->getId(),
                        'slug' => $catalog->getSlug(),
                        'title' => $catalog->getLatestMetadata()->getTitle()->toArray(),
                        'datasets' => [],
                    ];
                }

                $resources['catalogs'][$catalog->getId()]['datasets'][] = $resources['datasets'][$dataset->getId()];
            }
        }

        return array_values($resources['catalogs']);
    }
}
