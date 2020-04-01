<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Record;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Distribution\RDFDistribution\RDFDistribution;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Model\Castor\ApiClient;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class RenderRDFDistributionCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    /** @var Environment $twig */
    private $twig;

    public function __construct(ApiClient $apiClient, Environment $twig)
    {
        $this->apiClient = $apiClient;
        $this->twig = $twig;
    }

    /**
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws Exception
     */
    public function __invoke(RenderRDFDistributionCommand $message): string
    {
        $template = $this->twig->createTemplate($message->getDistribution()->getTwig());
        $this->apiClient->setToken($message->getUser()->getToken());
        $study = $this->apiClient->getStudy($message->getDistribution()->getDataset()->getStudy()->getId());

        $return = '';

        foreach ($message->getRecords() as $record) {
            $return .= $this->renderRecord($template, $message->getDistribution(), $study, $record) . "\n\n";
        }

        $return = $message->getDistribution()->getPrefix() . "\n\n" . $return;

        return $return;
    }

    /**
     */
    private function renderRecord(TemplateWrapper $template, RDFDistribution $distribution, Study $study, Record $record): string
    {
        $record = $this->apiClient->getRecordDataCollection($study, $record);

        $templateData = [
            'url' => $distribution->getRDFUrl(),
            'record' => $record,
        ];

        $content = $template->render($templateData);

        $trimmedContent = trim(preg_replace('/\n\s*\n/', "\n", $content));
        $trimmedContent = trim(preg_replace('/\t+/', '', $trimmedContent));

        return $trimmedContent;
    }
}
