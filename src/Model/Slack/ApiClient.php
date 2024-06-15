<?php
declare(strict_types=1);

namespace App\Model\Slack;

use App\Entity\Study;
use GuzzleHttp\Client;
use Throwable;
use function array_unique;
use function count;
use function implode;

class ApiClient
{
    private Client $client;

    public function __construct(private string $webhookUrl = '')
    {
        $this->client = new Client();
    }

    /** @param array<mixed> $message */
    public function postMessage(array $message): void
    {
        try {
            $this->client->request('POST', $this->webhookUrl, ['json' => $message]);
        } catch (Throwable) {
        }
    }

    public function postStudyMetadataNotification(Study $study, string $url): void
    {
        $metadata = $study->getLatestMetadata();

        $type = $metadata->getType()->toString();
        $method = $metadata->getMethodType()->toString();
        $condition = $metadata->getCondition()?->getText() ?? 'N/A';
        $intervention = $metadata->getIntervention()?->getText() ?? 'N/A';
        $estimatedEnrollment = $metadata->getEstimatedEnrollment() ?? 'N/A';
        $estimatedStudyStartDate = $metadata->getEstimatedStudyStartDate()?->format('Y-m-d') ?? 'N/A';
        $estimatedStudyCompletionDate = $metadata->getEstimatedStudyCompletionDate()?->format('Y-m-d') ?? 'N/A';

        $organizationArray = [];
        $countryArray = [];

        foreach ($metadata->getOrganizations() as $organization) {
            $organizationArray[] = $organization->getName();
            $countryArray[] = $organization->getCountry()->getName();
        }

        $organizations = count($organizationArray) > 0 ? implode(', ', array_unique($organizationArray)) : 'N/A';
        $countries = count($countryArray) > 0 ? implode(', ', array_unique($countryArray)) : 'N/A';

        $updated = $study->getMetadata()->count() > 1 ? true : ($metadata->getUpdatedAt() !== null);
        $header = $updated ? 'The metadata for a study were edited' : 'A new study was added to the FAIR Data Point';

        $message = [
            'text' => $header,
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => $header . ":\n*<" . $url . '|' . $metadata->getBriefName() . '>*',
                    ],
                ],
                [
                    'type' => 'section',
                    'fields' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Organization:*\n" . $organizations,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Country:*\n" . $countries,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Type:*\n" . $type,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Method:*\n" . $method,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Condition:*\n" . $condition,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Intervention:*\n" . $intervention,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Start:*\n" . $estimatedStudyStartDate,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Completion:*\n" . $estimatedStudyCompletionDate,
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Enrollment:*\n" . $estimatedEnrollment,
                        ],
                    ],
                ],
            ],
        ];

        $this->postMessage($message);
    }
}
