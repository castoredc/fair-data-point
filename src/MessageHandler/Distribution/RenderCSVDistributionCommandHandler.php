<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use App\Entity\Castor\Study;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\RenderCSVDistributionCommand;
use App\MessageHandler\CSVCommandHandler;
use App\Model\Castor\ApiClient;
use App\Type\DistributionAccessType;
use Cocur\Slugify\Slugify;
use Exception;
use function count;

class RenderCSVDistributionCommandHandler extends CSVCommandHandler
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function __invoke(RenderCSVDistributionCommand $message): string
    {
        if ($message->getDistribution()->getAccessRights() === DistributionAccessType::PUBLIC) {
            $this->apiClient->useApiUser($message->getCatalog()->getApiUser());
        } else {
            $this->apiClient->setUser($message->getUser());
        }

        $study = $this->apiClient->getStudy($message->getDistribution()->getDistribution()->getDataset()->getStudy()->getId());
        $studyFields = $this->apiClient->getPhasesAndSteps($study, true)->getFields();
        $slugify = new Slugify(['separator' => '_']);

        $data = [];
        $fields = [];
        $columns = ['record_id'];

        foreach ($studyFields as $field) {
            /** @var Field $field */
            if (! $message->getDistribution()->isFieldIncluded($field)) {
                continue;
            }

            $fields[] = $field;
            $columns[$field->getId()] = $field->getVariableName() ?? $slugify->slugify($field->getLabel());
        }

        foreach ($message->getRecords() as $record) {
            $recordData = $this->renderRecord($fields, $columns, $study, $record);

            if (count($recordData) <= 0) {
                continue;
            }
            $recordData['record_id'] = $record->getId();
            $data[] = $recordData;
        }

        return $this->generateCsv($columns, $data);
    }

    /**
     * @param Field[]  $fields
     * @param string[] $columns
     *
     * @return string[]
     *
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function renderRecord(array $fields, array $columns, Study $study, Record $record): array
    {
        $record = $this->apiClient->getRecordDataCollection($study, $record);
        $studyData = $record->getData()->getStudy();

        $data = [];

        foreach ($fields as $field) {
            $result = $studyData->getFieldResultByFieldId($field->getId());
            $value = $result !== null ? $result->getValue() : null;

            $column = $columns[$field->getId()];
            $data[$column] = $value;
        }

        return $data;
    }
}
