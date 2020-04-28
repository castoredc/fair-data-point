<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Record;
use App\Entity\Castor\Study;
use App\Entity\Data\RDF\RDFDistribution;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Model\Castor\ApiClient;
use App\Type\DistributionAccessType;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
        if ($message->getDistribution()->getAccessRights() === DistributionAccessType::PUBLIC) {
            $this->apiClient->useApiUser($message->getCatalog()->getApiUser());
        } else {
            $this->apiClient->setUser($message->getUser());
        }

        $study = $this->apiClient->getStudy($message->getDistribution()->getDistribution()->getDataset()->getStudy()->getId());

        $return = '';

        foreach ($message->getRecords() as $record) {
            $return .= $this->renderRecord($message->getDistribution(), $study, $record) . "\n\n";
        }

        return $return;
    }

    private function renderRecord(RDFDistribution $distribution, Study $study, Record $record): string
    {
        // TODO: Render RDF
        $record = $this->apiClient->getRecordDataCollection($study, $record);

        return '';
    }
}
