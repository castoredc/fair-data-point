<?php
declare(strict_types=1);

namespace App\Api\Controller\Agent;

use App\Api\Controller\ApiController;
use App\Api\Request\Agent\OrganizationApiRequest;
use App\Api\Resource\Agent\Department\DepartmentsApiResource;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\Agent\Organization\OrganizationSearchApiResource;
use App\Command\Agent\FindOrganizationsCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Exception\ApiRequestParseError;
use App\Exception\NotFound;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/agent/organization')]
class OrganizationApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_agent_find_organization')]
    public function findOrganization(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            $parsed = $this->parseRequest(OrganizationApiRequest::class, $request);
            assert($parsed instanceof OrganizationApiRequest);

            $envelope = $bus->dispatch(new FindOrganizationsCommand($parsed->getCountry(), $parsed->getSearch()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new OrganizationSearchApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NotFound) {
                return new JsonResponse([], Response::HTTP_NOT_FOUND);
            }

            $this->logger->critical('An error occurred while searching for an organization', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{organization}', methods: ['GET'], name: 'api_agent_organization')]
    public function organization(#[MapEntity(mapping: ['organization' => 'id'])]
    Organization $organization,): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse((new OrganizationApiResource($organization))->toArray());
    }

    #[Route(path: '/{organization}/department', methods: ['GET'], name: 'api_agent_organization_department')]
    public function departments(#[MapEntity(mapping: ['organization' => 'id'])]
    Organization $organization,): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse((new DepartmentsApiResource($organization->getDepartments()->toArray(), false))->toArray());
    }
}
