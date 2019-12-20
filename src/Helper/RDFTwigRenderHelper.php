<?php
declare(strict_types=1);

namespace App\Helper;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Distribution\RDFDistribution;
use App\Model\Castor\ApiClient;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;
use function in_array;
use function preg_replace;
use function trim;

class RDFTwigRenderHelper
{
    /** @var ApiClient */
    private $client;

    /** @var Study */
    private $study;

    /** @var TemplateWrapper */
    private $twig;

    /** @var Environment */
    private $environment;

    /** @var RDFDistribution */
    private $distribution;

    /** @var array<mixed> */
    private $metadata;

    /** @var array<mixed> */
    private $fields;

    /** @var array<string> */
    private $variables;

    public const METADATA_NAME = 'SNOMED';

    public const OPTION_GROUP_FIELDS = [
        'radio',
        'dropdown',
        'checkbox',
    ];

    public function __construct(ApiClient $client, Study $study, Environment $environment, RDFDistribution $distribution)
    {
        $this->client = $client;
        $this->study = $study;
        $this->environment = $environment;
        $this->distribution = $distribution;

        try {
            $this->twig = $environment->createTemplate($distribution->getTwig());
        } catch (LoaderError $e) {
        } catch (SyntaxError $e) {
        }

        $this->fields = [];
        $this->variables = [];

        $this->getData();
    }

    private function getData(): void
    {
        $this->metadata = $this->client->getRawMetadata($this->study->getId());
        $apiFields = $this->client->getRawFields($this->study->getId());

        foreach ($apiFields as $field) {
            $this->variables[$field['id']] = $field['field_variable_name'];
            $this->fields[$field['id']] = $field;
        }
    }

    public function renderRecord(string $recordId): string
    {
        $templateData = [
            'distribution' => $this->distribution,
            'record' => $this->getRecord($recordId),
        ];

        $content = $this->twig->render($templateData);

        $trimmedContent = trim(preg_replace('/\n\s*\n/', "\n", $content));
        $trimmedContent = trim(preg_replace('/\t+/', '', $trimmedContent));

        return $trimmedContent;
    }

    public function renderRecords(): string
    {
        $records = $this->client->getRawRecords($this->study->getId());
        $return = '';

        foreach ($records as $record) {
            if ($record['archived']) {
                continue;
            }

            $return .= $this->renderRecord($record['record_id']) . "\n\n";
        }

        return $return;
    }

    /**
     * @return array<mixed>
     */
    private function getRecord(string $recordId): array
    {
        $values = $this->client->getRawRecordDataPoints($this->study->getId(), $recordId);

        $return = [
            'record_id' => $recordId,
            'data' => [],
        ];

        foreach ($values as $value) {
            $fieldId = $value['field_id'];
            $fieldVariable = $this->variables[$value['field_id']];

            if (in_array($this->fields[$fieldId]['field_type'], self::OPTION_GROUP_FIELDS, true) && isset($this->metadata[$fieldId]) && isset($this->metadata[$fieldId][$value['field_value']])) {
                $return['data'][$fieldVariable] = $this->metadata[$fieldId][$value['field_value']][self::METADATA_NAME];
            } else {
                $return['data'][$fieldVariable] = $value['field_value'];
            }
        }

        return $return;
    }
}
