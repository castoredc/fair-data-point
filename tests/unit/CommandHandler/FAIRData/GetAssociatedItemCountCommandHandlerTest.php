<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\FAIRData;

use App\Command\FAIRData\GetAssociatedItemCountCommand;
use App\CommandHandler\FAIRData\GetAssociatedItemCountCommandHandler;
use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Study;
use App\Repository\CatalogRepository;
use App\Repository\DatasetRepository;
use App\Repository\DistributionRepository;
use App\Repository\StudyRepository;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class GetAssociatedItemCountCommandHandlerTest extends TestCase
{
    public function testCountsCatalogsForFairDataPoint(): void
    {
        $user = new User(null);
        [$em, $security, $repos] = $this->buildMocks($user, false);

        $repos['catalog']->expects(self::once())
            ->method('countCatalogs')
            ->with(null, null, null, $user)
            ->willReturn(5);

        $entity = $this->createMock(FAIRDataPoint::class);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame([ResourceType::catalog()->toString() => 5], $count->getCounts());
    }

    public function testCountsStudiesAndDatasetsForCatalogAsAdmin(): void
    {
        $user = new User(null);
        [$em, $security, $repos] = $this->buildMocks($user, true);

        $entity = $this->createMock(Catalog::class);

        $repos['study']->expects(self::once())
            ->method('countStudies')
            ->with($entity, null, null, false, true)
            ->willReturn(3);
        $repos['dataset']->expects(self::once())
            ->method('countDatasets')
            ->with($entity, null, null, null, $user)
            ->willReturn(7);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame([
            ResourceType::study()->toString() => 3,
            ResourceType::dataset()->toString() => 7,
        ], $count->getCounts());
    }

    public function testCountsStudiesForCatalogAsNonAdminPassesFalse(): void
    {
        $user = new User(null);
        [$em, $security, $repos] = $this->buildMocks($user, false);

        $entity = $this->createMock(Catalog::class);

        $repos['study']->expects(self::once())
            ->method('countStudies')
            ->with($entity, null, null, false, false)
            ->willReturn(0);
        $repos['dataset']->method('countDatasets')->willReturn(0);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame(0, $count->getCounts()[ResourceType::study()->toString()]);
    }

    public function testCountsDistributionsForDataset(): void
    {
        $user = new User(null);
        [$em, $security, $repos] = $this->buildMocks($user, false);

        $entity = $this->createMock(Dataset::class);

        $repos['distribution']->expects(self::once())
            ->method('countDistributions')
            ->with(null, $entity, null, $user)
            ->willReturn(4);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame([ResourceType::distribution()->toString() => 4], $count->getCounts());
    }

    public function testCountsDatasetsForStudy(): void
    {
        $user = new User(null);
        [$em, $security, $repos] = $this->buildMocks($user, false);

        $entity = $this->createMock(Study::class);

        $repos['dataset']->expects(self::once())
            ->method('countDatasets')
            ->with(null, $entity, null, null, $user)
            ->willReturn(2);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame([ResourceType::dataset()->toString() => 2], $count->getCounts());
    }

    public function testWorksWithNullUser(): void
    {
        [$em, $security, $repos] = $this->buildMocks(null, false);

        $repos['catalog']->expects(self::once())
            ->method('countCatalogs')
            ->with(null, null, null, null)
            ->willReturn(1);

        $entity = $this->createMock(FAIRDataPoint::class);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame([ResourceType::catalog()->toString() => 1], $count->getCounts());
    }

    public function testCountsDistributionForDistributionSubjectAreEmpty(): void
    {
        $user = new User(null);
        [$em, $security] = $this->buildMocks($user, false);

        // A Distribution subject matches none of the branches, so the count is empty.
        $entity = $this->createMock(Distribution::class);

        $handler = new GetAssociatedItemCountCommandHandler($em, $security);
        $count = $handler(new GetAssociatedItemCountCommand($entity));

        self::assertSame([], $count->getCounts());
    }

    /** @return array{0: EntityManagerInterface, 1: Security, 2: array<string, mixed>} */
    private function buildMocks(?User $user, bool $isAdmin): array
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);

        $security->method('getUser')->willReturn($user);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn($isAdmin);

        $catalogRepository = $this->createMock(CatalogRepository::class);
        $datasetRepository = $this->createMock(DatasetRepository::class);
        $distributionRepository = $this->createMock(DistributionRepository::class);
        $studyRepository = $this->createMock(StudyRepository::class);

        $em->method('getRepository')->willReturnMap([
            [Catalog::class, $catalogRepository],
            [Dataset::class, $datasetRepository],
            [Distribution::class, $distributionRepository],
            [Study::class, $studyRepository],
        ]);

        return [
            $em,
            $security,
            [
                'catalog' => $catalogRepository,
                'dataset' => $datasetRepository,
                'distribution' => $distributionRepository,
                'study' => $studyRepository,
            ],
        ];
    }
}
