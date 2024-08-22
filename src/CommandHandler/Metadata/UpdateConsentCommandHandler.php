<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\UpdateConsentCommand;
use App\Exception\NoAccessPermission;
use App\Model\Slack\ApiClient as SlackApiClient;
use App\Security\Authorization\Voter\StudyVoter;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateConsentCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private SlackApiClient $slackApiClient, private UriHelper $uriHelper, private Security $security)
    {
    }

    public function __invoke(UpdateConsentCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted(StudyVoter::EDIT, $study)) {
            throw new NoAccessPermission();
        }

        $metadata = $study->getLatestMetadata();
        $metadata->setConsentPublish($command->getPublish());
        $metadata->setConsentSocialMedia($command->getSocialMedia());

        $this->em->persist($metadata);

        $this->em->flush();

        $url = $this->uriHelper->getUri($study);

        $this->slackApiClient->postStudyMetadataNotification($study, $url);
    }
}
