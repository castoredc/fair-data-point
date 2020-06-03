<?php

namespace App\Service;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Study;
use App\Entity\Enum\CastorEntityType;
use App\Exception\InvalidEntityType;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use App\Repository\CastorEntityRepository;
use App\Security\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CastorEntityHelper
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(EntityManagerInterface $em, ApiClient $apiClient, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->apiClient = $apiClient;

        $user = $tokenStorage->getToken()->getUser();
        assert($user instanceof CastorUser);

        $this->apiClient->setUser($user);
    }

    public function getEntityFromDatabaseById(Study $study, string $id): ?CastorEntity
    {
        /** @var CastorEntityRepository $repository */
        $repository = $this->em->getRepository(CastorEntity::class);
        return $repository->findByIdAndStudy($study, $id);
    }

    public function getEntitiesFromDatabaseByType(Study $study, CastorEntityType $type): CastorEntityCollection
    {
        /** @var CastorEntityRepository $repository */
        $repository = $this->em->getRepository(CastorEntity::class);
        return new CastorEntityCollection($repository->findByStudyAndType($study, $type->getClassName()));
    }

    public function getEntitiesFromDatabaseByParent(Study $study, CastorEntity $parent): CastorEntityCollection
    {
        /** @var CastorEntityRepository $repository */
        $repository = $this->em->getRepository(CastorEntity::class);
        return new CastorEntityCollection($repository->findByStudyAndParent($study, $parent));
    }

    /**
     * @throws InvalidEntityType
     */
    public function getEntityFromCastorByTypeAndId(Study $study, CastorEntityType $type, string $id, string $parentId): CastorEntity
    {
        if ($type->isFieldOption()) {
            $optionGroup = $this->apiClient->getOptionGroup($study, $parentId);
            $entity = $optionGroup->getOptionById($id);
        } elseif($type->isFieldOptionGroup()) {
            $entity = $this->apiClient->getOptionGroup($study, $id);
        } else {
            throw new InvalidEntityType();
        }

        return $entity;
    }

    /**
     * @throws InvalidEntityType
     */
    public function getEntityByTypeAndId(Study $study, CastorEntityType $type, string $id, ?string $parentId = null) {
        $entity = null;

        $dbEntity = $this->getEntityFromDatabaseById($study, $id);
        $castorEntity = $this->getEntityFromCastorByTypeAndId($study, $type, $id, $parentId);

        if ($dbEntity === null) {
            // Entity not found in database, use the entity from Castor
            $entity = $castorEntity;

            if($entity->hasParent())
            {
                // Entity has parent, check if parent is available in database
                $dbParentEntity = $this->getEntityFromDatabaseById($study, $entity->getParent()->getId());

                if($dbParentEntity !== null) {
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

    public function getEntitiesByType(Study $study, CastorEntityType $type): CastorEntityCollection {
        $dbEntities = $this->getEntitiesFromDatabaseByType($study, $type);

        // Get entities from Castor
        if($type->isFieldOptionGroup()) {
            $castorEntities = $this->apiClient->getOptionGroups($study);
        } else {
            throw new InvalidEntityType();
        }

        // Merge entities
        // Entities from Castor are leading, since they contain more information

        foreach($dbEntities as $dbEntity)
        {
            /** @var CastorEntity $castorEntity */
            $castorEntity = $castorEntities->getById($dbEntity->getId());
            $castorEntity->setAnnotations($dbEntity->getAnnotations());

            if($castorEntity->hasChildren())
            {
                $dbChildren = $this->getEntitiesFromDatabaseByParent($study, $dbEntity);

                foreach($dbChildren as $dbChild) {
                    $castorChild = $castorEntity->getChild($dbChild->getId());

                    if($castorChild !== null) {
                        $castorChild->setAnnotations($dbChild->getAnnotations());
                    }
                }
            }
        }

        return $castorEntities;
    }

}