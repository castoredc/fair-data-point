<?php
declare(strict_types=1);

namespace App\Helper;

use App\Entity\Castor\Record;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Distribution\RDFDistribution;
use App\Model\Castor\ApiClient;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;
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

    /** @var RDFDistribution */
    private $distribution;

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
        $this->distribution = $distribution;

        try {
            $this->twig = $environment->createTemplate($distribution->getTwig());
        } catch (LoaderError $e) {
        } catch (SyntaxError $e) {
        }
    }

    public function renderRecord(Record $record): string
    {
        $dataCollection = $this->client->getRecordDataCollection($this->study, $record);

        $templateData = [
            'url' => $this->distribution->getRDFUrl(),
            'record' => $record,
        ];

        $content = $this->twig->render($templateData);

        $trimmedContent = trim(preg_replace('/\n\s*\n/', "\n", $content));
        $trimmedContent = trim(preg_replace('/\t+/', '', $trimmedContent));

        return $trimmedContent;
    }

    public function renderRecords(): string
    {
        $records = $this->client->getRecords($this->study);
        $return = '';

        foreach ($records as $record) {
            $return .= $this->renderRecord($record) . "\n\n";
        }

        return $return;
    }
}
