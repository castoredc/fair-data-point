<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\UpdateConsentCommand;
use App\CommandHandler\Metadata\UpdateConsentCommandHandler;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Study;
use App\Exception\NoAccessPermission;
use App\Model\Slack\ApiClient as SlackApiClient;
use App\Security\Authorization\Voter\StudyVoter;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class UpdateConsentCommandHandlerTest extends TestCase
{
    public function testUpdatesConsentPersistsAndNotifiesSlack(): void
    {
        $metadata = $this->createMock(StudyMetadata::class);
        $metadata->expects(self::once())->method('setConsentPublish')->with(true);
        $metadata->expects(self::once())->method('setConsentSocialMedia')->with(false);

        $study = $this->createMock(Study::class);
        $study->method('getLatestMetadata')->willReturn($metadata);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(StudyVoter::EDIT, $study)->willReturn(true);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with($metadata);
        $em->expects(self::once())->method('flush');

        $uriHelper = $this->createMock(UriHelper::class);
        $uriHelper->method('getUri')->with($study)->willReturn('https://fdp.example.com/study/1');

        $slack = $this->createMock(SlackApiClient::class);
        $slack->expects(self::once())
            ->method('postStudyMetadataNotification')
            ->with($study, 'https://fdp.example.com/study/1');

        $handler = new UpdateConsentCommandHandler($em, $slack, $uriHelper, $security);
        $handler(new UpdateConsentCommand($study, true, false));
    }

    public function testThrowsWhenUserCannotEdit(): void
    {
        $study = $this->createMock(Study::class);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(StudyVoter::EDIT, $study)->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $slack = $this->createMock(SlackApiClient::class);
        $slack->expects(self::never())->method('postStudyMetadataNotification');

        $handler = new UpdateConsentCommandHandler(
            $em,
            $slack,
            $this->createMock(UriHelper::class),
            $security,
        );

        $this->expectException(NoAccessPermission::class);

        $handler(new UpdateConsentCommand($study, true, true));
    }
}
