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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/agent/organization")
 */
class OrganizationApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_agent_find_organization")
     */
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
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NotFound) {
                return new JsonResponse([], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{organization}", methods={"GET"}, name="api_agent_organization")
     * @ParamConverter("organization", options={"mapping": {"organization": "id"}})
     */
    public function organization(Organization $organization): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse((new OrganizationApiResource($organization))->toArray());
    }

    /**
     * @Route("/{organization}/department", methods={"GET"}, name="api_agent_organization_department")
     * @ParamConverter("organization", options={"mapping": {"organization": "id"}})
     */
    public function departments(Organization $organization): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse((new DepartmentsApiResource($organization->getDepartments()->toArray(), false))->toArray());
    }
}
