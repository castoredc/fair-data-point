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
    /** @var Client */
    private $client;

    /** @var string */
    private $webhookUrl;

    public function __construct(string $webhookUrl = '')
    {
        $this->client = new Client();
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * @param array<mixed> $message
     */
    public function postMessage(array $message): void
    {
        try {
            $this->client->request('POST', $this->webhookUrl, ['json' => $message]);
        } catch (Throwable $e) {
        }
    }

    public function postStudyMetadataNotification(Study $study): void
    {
        $metadata = $study->getLatestMetadata();
        $dataset = $study->getDataset();

        $type = $metadata->getType()->toString();
        $method = $metadata->getMethodType() !== null ? $metadata->getMethodType()->toString() : 'N/A';
        $condition = $metadata->getCondition() !== null ? $metadata->getCondition()->getText() : 'N/A';
        $intervention = $metadata->getIntervention() !== null ? $metadata->getIntervention()->getText() : 'N/A';
        $estimatedEnrollment = $metadata->getEstimatedEnrollment() ?? 'N/A';
        $estimatedStudyStartDate = $metadata->getEstimatedStudyStartDate() !== null ? $metadata->getEstimatedStudyStartDate()->format('Y-m-d') : 'N/A';
        $estimatedStudyCompletionDate = $metadata->getEstimatedStudyCompletionDate() !== null ? $metadata->getEstimatedStudyCompletionDate()->format('Y-m-d') : 'N/A';

        $organizationArray = [];
        $countryArray = [];

        foreach ($metadata->getOrganizations() as $organization) {
            $organizationArray[] = $organization->getName();
            $countryArray[] = $organization->getCountry()->getName();
        }

        $organizations = count($organizationArray) > 0 ? implode(', ', array_unique($organizationArray)) : 'N/A';
        $countries = count($countryArray) > 0 ? implode(', ', array_unique($countryArray)) : 'N/A';

        $updated = $study->getMetadata()->count() > 1 ? true : ($metadata->getUpdated() !== null);
        $header = $updated ? 'The metadata for a study were edited' : 'A new study was added to the FAIR Data Point';

        $message = [
            'text' => $header,
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => $header . ":\n*<" . $dataset->getAccessUrl() . '|' . $metadata->getBriefName() . '>*',
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
