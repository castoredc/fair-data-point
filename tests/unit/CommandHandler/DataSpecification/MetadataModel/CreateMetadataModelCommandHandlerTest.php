<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelCommand;
use App\CommandHandler\DataSpecification\MetadataModel\CreateMetadataModelCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateMetadataModelCommandHandlerTest extends TestCase
{
    public function testCreatesMetadataModelWithDefaultVersionAndNodes(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $handler = new CreateMetadataModelCommandHandler($em, $security);
        $result = $handler(new CreateMetadataModelCommand('My model', 'A description'));

        self::assertSame('My model', $result->getTitle());
        self::assertSame('A description', $result->getDescription());

        $version = $result->getLatestVersion();
        self::assertInstanceOf(MetadataModelVersion::class, $version);
        self::assertSame(
            CreateMetadataModelCommandHandler::DEFAULT_VERSION_NUMBER,
            $version->getVersion()->getValue()
        );

        // 5 record nodes, 4 children nodes, 4 parents nodes.
        $elements = $version->getElements();
        self::assertCount(13, $elements);

        $recordCount = $childrenCount = $parentsCount = 0;
        foreach ($elements as $element) {
            if ($element instanceof RecordNode) {
                $recordCount++;
            } elseif ($element instanceof ChildrenNode) {
                $childrenCount++;
            } elseif ($element instanceof ParentsNode) {
                $parentsCount++;
            }
        }

        self::assertSame(5, $recordCount);
        self::assertSame(4, $childrenCount);
        self::assertSame(4, $parentsCount);

        // The model, the version and the explicitly-persisted nodes.
        // Note: the study record node is added to the version but never
        // explicitly persisted (it relies on cascade), so only 12 of the
        // 13 nodes are passed to persist() directly: model + version + 12.
        self::assertContains($result, $persisted);
        self::assertContains($version, $persisted);
        self::assertCount(14, $persisted);
    }

    public function testThrowsWhenNotAdmin(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(false);

        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new CreateMetadataModelCommandHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler(new CreateMetadataModelCommand('My model', null));
    }
}
