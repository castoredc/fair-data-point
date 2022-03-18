<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Institute;
use App\Entity\Castor\Record;
use App\Entity\Enum\CastorEntityType;
use App\Entity\FAIRData\Country;
use App\Exception\InvalidEntityType;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use App\Security\ApiUser;
use App\Security\Providers\Castor\CastorUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use function assert;

class CastorEntityHelper
{
    private EntityManagerInterface $em;
    private ApiClient $apiClient;
    private EncryptionService $encryptionService;

    /** @throws UserNotACastorUser */
    public function __construct(EntityManagerInterface $em, ApiClient $apiClient, EncryptionService $encryptionService)
    {
        $this->em = $em;
        $this->apiClient = $apiClient;
        $this->encryptionService = $encryptionService;
    }

    public function useApiUser(ApiUser $user): void
    {
        $this->apiClient->useApiUser($user, $this->encryptionService);
    }

    public function useUser(CastorUser $user): void
    {
        $this->apiClient->setUser($user);
    }

    public function getEntityFromDatabaseById(CastorStudy $study, string $id): ?CastorEntity
    {
        $repository = $this->em->getRepository(CastorEntity::class);

        return $repository->findByIdAndStudy($study, $id);
    }

    public function getEntitiesFromDatabaseByType(CastorStudy $study, CastorEntityType $type): CastorEntityCollection
    {
        $repository = $this->em->getRepository(CastorEntity::class);

        return new CastorEntityCollection($repository->findByStudyAndType($study, $type->getClassName()));
    }

    public function getEntitiesFromDatabaseByParent(CastorStudy $study, CastorEntity $parent): CastorEntityCollection
    {
        $repository = $this->em->getRepository(CastorEntity::class);

        return new CastorEntityCollection($repository->findByStudyAndParent($study, $parent));
    }

    /** @throws InvalidEntityType */
    public function getEntityFromCastorByTypeAndId(CastorStudy $study, CastorEntityType $type, string $id, ?string $parentId = null): CastorEntity
    {
        if ($type->isFieldOption()) {
            $optionGroup = $this->apiClient->getOptionGroup($study, $parentId);
            $entity = $optionGroup->getOptionById($id);
        } elseif ($type->isFieldOptionGroup()) {
            $entity = $this->apiClient->getOptionGroup($study, $id);
        } elseif ($type->isField()) {
            $entity = $this->apiClient->getField($study, $id);
        } elseif ($type->isReport()) {
            $entity = $this->apiClient->getReport($study, $id);
        } elseif ($type->isSurvey()) {
            $entity = $this->apiClient->getSurvey($study, $id);
        } else {
            throw new InvalidEntityType();
        }

        return $entity;
    }

    /** @throws InvalidEntityType */
    public function getEntityByTypeAndId(CastorStudy $study, CastorEntityType $type, string $id, ?string $parentId = null): CastorEntity
    {
        $entity = null;

        $dbEntity = $this->getEntityFromDatabaseById($study, $id);
        $castorEntity = $this->getEntityFromCastorByTypeAndId($study, $type, $id, $parentId);

        if ($dbEntity === null) {
            // Entity not found in database, use the entity from Castor
            $entity = $castorEntity;

            if ($entity->hasParent()) {
                // Entity has parent, check if parent is available in database
                $dbParentEntity = $this->getEntityFromDatabaseById($study, $entity->getParent()->getId());

                if ($dbParentEntity !== null) {
                    // Parent entity found in database, attach to entity
                    $entity->setParent($dbParentEntity);
                }
            }
        } else {
            // Entity found in database, update information from Castor
            $entity = $dbEntity;
            $entity->setLabel($entity->getLabel());
        }

        return $entity;
    }

    public function getEntitiesByType(CastorStudy $study, CastorEntityType $type): CastorEntityCollection
    {
        $dbEntities = $this->getEntitiesFromDatabaseByType($study, $type);

        // Get entities from Castor
        if (! $type->isFieldOptionGroup()) {
            throw new InvalidEntityType();
        }

        $castorEntities = $this->apiClient->getOptionGroups($study);

        // Merge entities
        // Entities from Castor are leading, since they contain more information

        foreach ($dbEntities as $dbEntity) {
            $castorEntity = $castorEntities->getById($dbEntity->getId());

            if ($castorEntity === null) {
                continue;
            }

            $castorEntity->setAnnotations($dbEntity->getAnnotations());

            if (! $castorEntity->hasChildren()) {
                continue;
            }

            $dbChildren = $this->getEntitiesFromDatabaseByParent($study, $dbEntity);

            foreach ($dbChildren as $dbChild) {
                $castorChild = $castorEntity->getChild($dbChild->getId());

                if ($castorChild === null) {
                    continue;
                }

                $castorChild->setAnnotations($dbChild->getAnnotations());
            }
        }

        return $castorEntities;
    }

    public function getInstitutes(CastorStudy $study): ArrayCollection
    {
        $institutes = new ArrayCollection();

        $repository = $this->em->getRepository(Institute::class);

        $countryRepository = $this->em->getRepository(Country::class);
        $countries = $countryRepository->getAllCountriesWithCastorIds();

        $castorInstitutes = $this->apiClient->getInstitutes($study);
        $dbInstitutes = $repository->findByStudy($study);

        foreach ($castorInstitutes as $castorInstitute) {
            assert($castorInstitute instanceof Institute);

            $dbInstitute = $dbInstitutes->get($castorInstitute->getId());
            assert($dbInstitute instanceof Institute || $dbInstitute === null);

            if ($dbInstitute === null) {
                $dbInstitute = $castorInstitute;
            } else {
                $dbInstitute->setName($castorInstitute->getName());
                $dbInstitute->setCode($castorInstitute->getCode());
                $dbInstitute->setAbbreviation($castorInstitute->getAbbreviation());
                $dbInstitute->setCountry($castorInstitute->getCountry());
                $dbInstitute->setCountryId($castorInstitute->getCountryId());
                $dbInstitute->setDeleted($castorInstitute->isDeleted());
            }

            $country = $countries->get($dbInstitute->getCountryId());
            $dbInstitute->setCountry($country);

            $institutes->set($dbInstitute->getId(), $dbInstitute);
        }

        return $institutes;
    }

    public function getRecords(CastorStudy $study): ArrayCollection
    {
        $institutes = $this->getInstitutes($study);
        $records = new ArrayCollection();

        $repository = $this->em->getRepository(Record::class);

        $castorRecords = $this->apiClient->getRecords($study, $institutes);
        $dbRecords = $repository->findByStudy($study);

        foreach ($castorRecords as $castorRecord) {
            assert($castorRecord instanceof Record);

            $dbRecord = $dbRecords->get($castorRecord->getId());
            assert($dbRecord instanceof Record || $dbRecord === null);

            if ($dbRecord === null) {
                $dbRecord = $castorRecord;
            } else {
                $dbRecord->setUpdatedOn($castorRecord->getUpdatedOn());
                $dbRecord->setInstitute($castorRecord->getInstitute());
            }

            $records->set($dbRecord->getId(), $dbRecord);
        }

        return $records;
    }
}
