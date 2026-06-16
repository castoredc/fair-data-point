<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\RenderRDFMetadataCommand;
use App\CommandHandler\Metadata\RenderRDFMetadataCommandHandler;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Exception\NoAccessPermission;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class RenderRDFMetadataCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserCannotView(): void
    {
        $entity = $this->createMock(MetadataEnrichedEntity::class);

        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with('view', $entity)
            ->willReturn(false);

        $handler = new RenderRDFMetadataCommandHandler(
            $this->createMock(EntityManagerInterface::class),
            $security,
            $this->createMock(UriHelper::class),
        );

        $this->expectException(NoAccessPermission::class);

        $handler(new RenderRDFMetadataCommand($entity));
    }

    public function testReturnsEmptyGraphWhenEntityHasNoMetadata(): void
    {
        $entity = $this->createMock(MetadataEnrichedEntity::class);
        $entity->method('getLatestMetadata')->willReturn(null);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('view', $entity)->willReturn(true);

        $handler = new RenderRDFMetadataCommandHandler(
            $this->createMock(EntityManagerInterface::class),
            $security,
            $this->createMock(UriHelper::class),
        );

        $graph = $handler(new RenderRDFMetadataCommand($entity));

        self::assertTrue($graph->isEmpty());
    }
}
