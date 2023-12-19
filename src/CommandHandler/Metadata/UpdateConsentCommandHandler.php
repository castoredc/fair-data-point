<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\UpdateConsentCommand;
use App\Exception\NoAccessPermission;
use App\Model\Slack\ApiClient as SlackApiClient;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;

#[AsMessageHandler]
class UpdateConsentCommandHandler
{
    private EntityManagerInterface $em;
    private SlackApiClient $slackApiClient;
    private UriHelper $uriHelper;
    private Security $security;

    public function __construct(EntityManagerInterface $em, SlackApiClient $slackApiClient, UriHelper $uriHelper, Security $security)
    {
        $this->em = $em;
        $this->slackApiClient = $slackApiClient;
        $this->uriHelper = $uriHelper;
        $this->security = $security;
    }

    public function __invoke(UpdateConsentCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
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
