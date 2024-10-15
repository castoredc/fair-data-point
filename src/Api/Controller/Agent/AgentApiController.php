<?php
declare(strict_types=1);

namespace App\Api\Controller\Agent;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\MetadataFilterApiRequest;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudiesMapApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Command\Agent\GetAgentAssociatedMetadataCountCommand;
use App\Command\Catalog\GetPaginatedCatalogsCommand;
use App\Command\Dataset\GetPaginatedDatasetsCommand;
use App\Command\Distribution\GetPaginatedDistributionsCommand;
use App\Command\Study\FilterStudiesCommand;
use App\Command\Study\GetPaginatedStudiesCommand;
use App\Command\Study\GetStudiesForAgentCommand;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\Authorization\Voter\DistributionVoter;
use App\Security\Authorization\Voter\StudyVoter;
use App\Security\User;
use App\Service\UriHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/agent/details/{agent}')]
class AgentApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_agent_details')]
    public function agentDetails(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        MessageBusInterface $bus,
    ): Response {
        try {
            $envelope = $bus->dispatch(new GetAgentAssociatedMetadataCountCommand($agent));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            if ($agent instanceof Organization) {
                $resource = new OrganizationApiResource($agent);
            } elseif ($agent instanceof Person) {
                $resource = new PersonApiResource($agent);
            } else {
                return new JsonResponse([], Response::HTTP_NOT_FOUND);
            }

            $resource->setMetadataCount($results);

            return new JsonResponse($resource->toArray());
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the details of an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/study', methods: ['GET'], name: 'api_agent_studies')]
    public function agentStudies(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedStudiesCommand(
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    null,
                    $agent,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    null
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                StudyApiResource::class,
                $results,
                [StudyVoter::VIEW, StudyVoter::EDIT, StudyVoter::EDIT_SOURCE_SYSTEM]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the studies for an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/study/filters', methods: ['GET'], name: 'api_agent_study_filters')]
    public function agentStudiesFilters(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        MessageBusInterface $bus,
    ): Response {
        try {
            $envelope = $bus->dispatch(new GetStudiesForAgentCommand($agent));
            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $studies = $handledStamp->getResult();

            return new JsonResponse((new StudiesFilterApiResource($studies))->toArray());
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the study filters for an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/dataset', methods: ['GET'], name: 'api_agent_datasets')]
    public function agentDatasets(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $parsed = $this->parseRequest(MetadataFilterApiRequest::class, $request);
            assert($parsed instanceof MetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedDatasetsCommand(
                    null,
                    $agent,
                    $user,
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    $parsed->getSearch(),
                    $parsed->getHideParents()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                DatasetApiResource::class,
                $results,
                [DatasetVoter::VIEW, DatasetVoter::EDIT, DatasetVoter::MANAGE]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the datasets for an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/catalog', methods: ['GET'], name: 'api_agent_catalogs')]
    public function agentCatalogs(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        try {
            $parsed = $this->parseRequest(MetadataFilterApiRequest::class, $request);
            assert($parsed instanceof MetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedCatalogsCommand(
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    $parsed->getSearch(),
                    $agent,
                    null,
                    null
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                CatalogApiResource::class,
                $results,
                [CatalogVoter::VIEW, CatalogVoter::ADD, CatalogVoter::EDIT, CatalogVoter::MANAGE]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the catalogs for an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/distribution', methods: ['GET'], name: 'api_agent_distribution')]
    public function agentDistributions(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        Request $request,
        MessageBusInterface $bus,
        UriHelper $uriHelper,
    ): Response {
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $parsed = $this->parseRequest(MetadataFilterApiRequest::class, $request);
            assert($parsed instanceof MetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedDistributionsCommand(
                    null,
                    null,
                    $agent,
                    $user,
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    $parsed->getSearch()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                DistributionApiResource::class,
                $results,
                [DistributionVoter::VIEW, DistributionVoter::EDIT, DistributionVoter::MANAGE, DistributionVoter::ACCESS_DATA],
                $uriHelper
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the distributions for an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/map', methods: ['GET'], name: 'api_agent_map')]
    public function agentMap(
        #[MapEntity(mapping: ['agent' => 'slug'])]
        Agent $agent,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new FilterStudiesCommand(
                    null,
                    $agent,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new StudiesMapApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the map information for an agent',
                [
                    'exception' => $e,
                    'Agent' => $agent->getSlug(),
                    'AgentID' => $agent->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
