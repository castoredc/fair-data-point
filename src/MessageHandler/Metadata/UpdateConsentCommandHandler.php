<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Exception\NoAccessPermission;
use App\Message\Metadata\UpdateConsentCommand;
use App\Model\Slack\ApiClient as SlackApiClient;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateConsentCommandHandler implements MessageHandlerInterface
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

    public function __invoke(UpdateConsentCommand $message): void
    {
        $study = $message->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $metadata = $study->getLatestMetadata();
        $metadata->setConsentPublish($message->getPublish());
        $metadata->setConsentSocialMedia($message->getSocialMedia());

        $this->em->persist($metadata);

        $this->em->flush();

        $url = $this->uriHelper->getUri($study);

        $this->slackApiClient->postStudyMetadataNotification($study, $url);
    }
}
