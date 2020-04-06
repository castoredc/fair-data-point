<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Field;
use App\Entity\Castor\Record;
use App\Entity\Castor\Study;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\RenderCSVDistributionCommand;
use App\Model\Castor\ApiClient;
use App\Type\DistributionAccessType;
use Cocur\Slugify\Slugify;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function count;
use function fclose;
use function feof;
use function fopen;
use function fputcsv;
use function fread;
use function rewind;

class RenderCSVDistributionCommandHandler implements MessageHandlerInterface
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

        $study = $this->apiClient->getStudy($message->getDistribution()->getDataset()->getStudy()->getId());
        $slugify = new Slugify();

        $data = [];
        $fields = [];
        $columns = [];

        foreach ($study->getFields() as $field) {
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

    /**
     * @param string[]     $columns
     * @param array<mixed> $data
     */
    private function generateCsv(array $columns, array $data, string $delimiter = ',', string $enclosure = '"'): string
    {
        $handle = fopen('php://temp', 'r+');
        $contents = null;

        if (count($data) === 0) {
            return '';
        }

        fputcsv($handle, $columns, $delimiter, $enclosure);

        foreach ($data as $line) {
            fputcsv($handle, $line, $delimiter, $enclosure);
        }

        rewind($handle);

        while (! feof($handle)) {
            $contents .= fread($handle, 8192);
        }

        fclose($handle);

        return $contents;
    }
}
