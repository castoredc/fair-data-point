<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\DataDictionary;

use App\Command\DataSpecification\DataDictionary\CreateDataDictionaryVersionCommand;
use App\CommandHandler\DataSpecification\DataDictionary\CreateDataDictionaryVersionCommandHandler;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Entity\Enum\VersionType;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Service\VersionNumberHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateDataDictionaryVersionCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotAdmin(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);
        $dictionary->addVersion(new DataDictionaryVersion(new Version('1.0.0')));

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = new CreateDataDictionaryVersionCommandHandler($em, $security, new VersionNumberHelper());

        $this->expectException(NoAccessPermission::class);
        $handler(new CreateDataDictionaryVersionCommand($dictionary, VersionType::minor()));
    }

    public function testDuplicatesGroupsAndBumpsVersion(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);
        $version = new DataDictionaryVersion(new Version('1.0.0'));
        $dictionary->addVersion($version);

        $group = new DataDictionaryGroup('Group', 1, false, false, $version);
        $version->setGroups(new ArrayCollection([$group]));

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new CreateDataDictionaryVersionCommandHandler($em, $security, new VersionNumberHelper());
        $newVersion = $handler(new CreateDataDictionaryVersionCommand($dictionary, VersionType::minor()));

        self::assertSame('1.1.0', $newVersion->getVersion()->getValue());
        self::assertCount(2, $dictionary->getVersions());
        self::assertSame($newVersion, $dictionary->getLatestVersion());

        self::assertCount(1, $newVersion->getGroups());
        $newGroup = $newVersion->getGroups()->first();
        self::assertInstanceOf(DataDictionaryGroup::class, $newGroup);
        self::assertSame('Group', $newGroup->getTitle());
        self::assertNotSame($group, $newGroup);
    }
}
