<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Encryption\EncryptionService;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\RenderCSVDistributionCommand;
use App\MessageHandler\CSVCommandHandler;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use App\Type\DistributionAccessType;
use Cocur\Slugify\Slugify;
use Exception;
use Symfony\Component\Security\Core\Security;
use function assert;
use function count;

class RenderCSVDistributionCommandHandler extends CSVCommandHandler
{
    /** @var ApiClient */
    private $apiClient;

    /** @var Security */
    private $security;

    /** @var EncryptionService */
    private $encryptionService;

    public function __construct(ApiClient $apiClient, Security $security, EncryptionService $encryptionService)
    {
        $this->apiClient = $apiClient;
        $this->security = $security;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @throws Exception
     */
    public function __invoke(RenderCSVDistributionCommand $message): string
    {
        $contents = $message->getDistribution();
        $distribution = $contents->getDistribution();

        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $dbStudy = $message->getDistribution()->getDistribution()->getDataset()->getStudy();
        assert($dbStudy instanceof CastorStudy);

        if ($message->getDistribution()->getAccessRights() === DistributionAccessType::PUBLIC) {
            $this->apiClient->useApiUser($message->getDistribution()->getDistribution()->getApiUser(), $this->encryptionService);
        } else {
            $this->apiClient->setUser($user);
        }

        $study = $this->apiClient->getStudy($dbStudy->getId());
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
            $columns[$field->getId()] = $field->getVariableName() ?? $slugify->slugify($field->getFieldLabel());
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
    private function renderRecord(array $fields, array $columns, CastorStudy $study, Record $record): array
    {
        $record = $this->apiClient->getRecordDataCollection($study, $record);
        $studyData = $record->getData()->getStudy();

        $data = [];

        foreach ($fields as $field) {
            $result = $studyData->getFieldResultByFieldId($field->getId());

            if ($result !== null) {
                $value = $result->getValue();
            } else {
                $value = null;
            }

            $column = $columns[$field->getId()];
            $data[$column] = $value;
        }

        return $data;
    }
}
