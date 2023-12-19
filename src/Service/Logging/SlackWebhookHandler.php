<?php
declare(strict_types=1);

namespace App\Service\Logging;

use App\Model\Slack\ApiClient;
use App\Security\User;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Security\Core\Security;
use function array_key_exists;
use function assert;
use function count;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;

class SlackWebhookHandler extends AbstractProcessingHandler
{
    public const COLOR_DANGER = 'danger';
    public const COLOR_WARNING = 'warning';
    public const COLOR_GOOD = 'good';

    private ApiClient $apiClient;

    private ?Security $security = null;

    private string $rootPath;

    public function __construct(
        string $webhookUrl = '',
        string $rootPath = '',
        ?Security $security = null
    ) {
        parent::__construct(Logger::CRITICAL, true);

        $this->apiClient = new ApiClient($webhookUrl);
        $this->rootPath = $rootPath;

        if ($security === null) {
            return;
        }

        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $record
     */
    protected function write(array|LogRecord $record): void
    {
        $message = $this->getSlackMessage($record);
        $this->apiClient->postMessage($message);
    }

    /**
     * @param mixed[] $record
     *
     * @return mixed[]
     */
    private function getSlackMessage(array $record): array
    {
        $header = ':rotating_light: An error occurred';

        $blocks = [];

        $blocks[] = [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => sprintf('*%s*', $header),
            ],
        ];

        $stackTrace = null;
        $message = $record['message'];
        $previousException = null;

        if (array_key_exists('context', $record) && count($record['context']) > 0) {
            $exception = array_key_exists('exception', $record['context']) ? $record['context']['exception'] : null;

            if ($exception !== null) {
                unset($record['context']['exception']);

                $exceptionClass = $exception::class;

                if ($exception instanceof HandlerFailedException) {
                    $exception = $exception->getPrevious();
                    $exceptionClass .= "\n" . $exception::class;
                }

                if ($exception->getPrevious() !== null) {
                    $previousException = $exception->getPrevious();
                    $exceptionClass .= sprintf("\n(%s)", $previousException::class);
                }

                $stackTrace = $exception->getTraceAsString();

                if (strlen($stackTrace) > 2500) {
                    $stackTrace = substr($stackTrace, 0, 2500);
                }

                $blocks[] = [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => sprintf("*Exception*\n%s", $exceptionClass),
                    ],
                ];

                $blocks[] = [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => sprintf("*File*\n%s:%s", str_replace($this->rootPath, '', $exception->getFile()), $exception->getLine()),
                    ],
                ];
            }
        }

        $blocks[] = [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => sprintf("*Message*\n%s", $message),
            ],
        ];

        $currentUser = '_Not logged in_';

        if ($this->security !== null && $this->security->getUser() !== null) {
            $user = $this->security->getUser();
            assert($user instanceof User);
            $currentUser = $user->getId();
        }

        $blocks[] = [
            'type' => 'section',
            'fields' => [
                [
                    'type' => 'mrkdwn',
                    'text' => sprintf("*Current user*\n%s", $currentUser),
                ],
                [
                    'type' => 'mrkdwn',
                    'text' => sprintf("*Level*\n%s", $record['level_name']),
                ],
            ],
        ];

        if (count($record['context']) > 0) {
            $contextFields = [];

            foreach ($record['context'] as $label => $context) {
                $contextFields[] = [
                    'type' => 'mrkdwn',
                    'text' => sprintf("*%s:*\n%s", $label, $context),
                ];
            }

            $blocks[] = [
                'type' => 'section',
                'fields' => $contextFields,
            ];
        }

        // if ($stackTrace !== null) {
        //     $blocks[] = [
        //         'type' => 'section',
        //         'text' => [
        //             'type' => 'mrkdwn',
        //             'text' => sprintf("*Stack trace*\n```%s```", $stackTrace),
        //         ],
        //     ];
        // }

        return [
            'text' => $header,
            'blocks' => $blocks,
        ];
    }
}
