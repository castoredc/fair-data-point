<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\CSV;

use App\Command\Distribution\CSV\RenderCSVDistributionCommand;
use App\CommandHandler\CSVCommandHandler;
use App\Entity\Castor\CastorStudy;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Security\User;
use App\Service\EncryptionService;
use App\Type\DistributionAccessType;
use Exception;
use Symfony\Component\Security\Core\Security;
use function assert;

class RenderCSVDistributionCommandHandler extends CSVCommandHandler
{
    private ApiClient $apiClient;

    private Security $security;

    private EncryptionService $encryptionService;

    public function __construct(ApiClient $apiClient, Security $security, EncryptionService $encryptionService)
    {
        $this->apiClient = $apiClient;
        $this->security = $security;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @throws Exception
     */
    public function __invoke(RenderCSVDistributionCommand $command): string
    {
        $contents = $command->getDistribution();
        $distribution = $contents->getDistribution();

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $dbStudy = $command->getDistribution()->getDistribution()->getDataset()->getStudy();
        assert($dbStudy instanceof CastorStudy);

        if ($command->getDistribution()->getAccessRights() === DistributionAccessType::PUBLIC) {
            $this->apiClient->useApiUser($command->getDistribution()->getDistribution()->getApiUser(), $this->encryptionService);
        } else {
            if (! $user->hasCastorUser()) {
                throw new UserNotACastorUser();
            }

            $this->apiClient->setUser($user->getCastorUser());
        }

        return '';

//        $study = $this->apiClient->getStudy($dbStudy->getId());
//        $studyFields = $this->apiClient->getPhasesAndSteps($study, true)->getFields();
//        $slugify = new Slugify(['separator' => '_']);
//
//        $data = [];
//        $fields = [];
//        $columns = ['record_id'];
//
//        foreach ($studyFields as $field) {
//            if (! $command->getDistribution()->isFieldIncluded($field)) {
//                continue;
//            }
//
//            $fields[] = $field;
//            $columns[$field->getId()] = $field->getVariableName() ?? $slugify->slugify($field->getFieldLabel());
//        }
//
//        foreach ($command->getRecords() as $record) {
//            $recordData = $this->renderRecord($fields, $columns, $study, $record);
//
//            if (count($recordData) <= 0) {
//                continue;
//            }
//
//            $recordData['record_id'] = $record->getId();
//            $data[] = $recordData;
//        }
//
//        return $this->generateCsv($columns, $data);
    }

//    /**
//     * @param Field[]  $fields
//     * @param string[] $columns
//     *
//     * @return string[]
//     *
//     * @throws ErrorFetchingCastorData
//     * @throws NoAccessPermission
//     * @throws NotFound
//     * @throws SessionTimedOut
//     */
//    private function renderRecord(array $fields, array $columns, CastorStudy $study, Record $record): array
//    {
//        $record = $this->apiClient->getRecordDataCollection($study, $record);
//        $studyData = $record->getData()->getStudy();
//
//        $data = [];
//
//        foreach ($fields as $field) {
//            $result = $studyData->getFieldResultByFieldId($field->getId());
//
//            if ($result !== null) {
//                $value = $result->getValue();
//            } else {
//                $value = null;
//            }
//
//            $column = $columns[$field->getId()];
//            $data[$column] = $value;
//        }
//
//        return $data;
//    }
}
