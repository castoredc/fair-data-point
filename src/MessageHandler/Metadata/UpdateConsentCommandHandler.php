<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Message\Metadata\UpdateConsentCommand;
use App\Model\Slack\ApiClient as SlackApiClient;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateConsentCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SlackApiClient  */
    private $slackApiClient;

    /** @var UriHelper  */
    private $uriHelper;

    public function __construct(EntityManagerInterface $em, SlackApiClient $slackApiClient, UriHelper $uriHelper)
    {
        $this->em = $em;
        $this->slackApiClient = $slackApiClient;
        $this->uriHelper = $uriHelper;
    }

    public function __invoke(UpdateConsentCommand $message): void
    {
        $metadata = $message->getStudy()->getLatestMetadata();
        $metadata->setConsentPublish($message->getPublish());
        $metadata->setConsentSocialMedia($message->getSocialMedia());

        $this->em->persist($metadata);

        $this->em->flush();

        $url = $this->uriHelper->getUri($message->getStudy());

        $this->slackApiClient->postStudyMetadataNotification($message->getStudy(), $url);
    }
}
