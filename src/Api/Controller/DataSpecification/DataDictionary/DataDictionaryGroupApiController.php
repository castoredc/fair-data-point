<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataDictionary;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\DataDictionary\DataDictionaryGroupApiRequest;
use App\Api\Resource\DataSpecification\DataDictionary\DataDictionaryGroupsApiResource;
use App\Command\DataSpecification\DataDictionary\CreateDataDictionaryGroupCommand;
use App\Command\DataSpecification\DataDictionary\DeleteDataDictionaryGroupCommand;
use App\Command\DataSpecification\DataDictionary\UpdateDataDictionaryGroupCommand;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/dictionary/{dataDictionary}/v/{version}/group')]
#[ParamConverter('dataDictionaryVersion', options: ['mapping' => ['dataDictionary' => 'data_dictionary', 'version' => 'id']])]
class DataDictionaryGroupApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_dictionary_groups')]
    public function getGroups(DataDictionaryVersion $dataDictionaryVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataDictionaryVersion->getDataDictionary());

        return new JsonResponse((new DataDictionaryGroupsApiResource($dataDictionaryVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_dictionary_group_add')]
    public function addGroup(DataDictionaryVersion $dataDictionaryVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataDictionaryVersion->getDataDictionary());

        try {
            $parsed = $this->parseRequest(DataDictionaryGroupApiRequest::class, $request);
            assert($parsed instanceof DataDictionaryGroupApiRequest);

            $bus->dispatch(new CreateDataDictionaryGroupCommand($dataDictionaryVersion, $parsed->getTitle(), $parsed->getOrder(), $parsed->isRepeated(), $parsed->isDependent(), $parsed->getDependencies()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data dictionary group', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{group}', methods: ['POST'], name: 'api_dictionary_group_update')]
    public function updateGroup(DataDictionaryVersion $dataDictionaryVersion, #[MapEntity(mapping: ['group' => 'id'])]
    DataDictionaryGroup $group, Request $request, MessageBusInterface $bus,): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $group->getVersion()->getDataSpecification());

        if ($group->getVersion() !== $dataDictionaryVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(DataDictionaryGroupApiRequest::class, $request);
            assert($parsed instanceof DataDictionaryGroupApiRequest);

            $bus->dispatch(new UpdateDataDictionaryGroupCommand($group, $parsed->getTitle(), $parsed->getOrder(), $parsed->isRepeated(), $parsed->isDependent(), $parsed->getDependencies()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data dictionary group', [
                'exception' => $e,
                'GroupID' => $group->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{group}', methods: ['DELETE'], name: 'api_dictionary_group_delete')]
    public function deleteGroup(DataDictionaryVersion $dataDictionaryVersion, #[MapEntity(mapping: ['group' => 'id'])]
    DataDictionaryGroup $group, MessageBusInterface $bus,): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $group->getVersion()->getDataSpecification());

        if ($group->getVersion() !== $dataDictionaryVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteDataDictionaryGroupCommand($group));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a data dictionary group', [
                'exception' => $e,
                'GroupID' => $group->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
