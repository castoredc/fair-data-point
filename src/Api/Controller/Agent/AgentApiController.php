<?php
declare(strict_types=1);

namespace App\Api\Controller\Agent;

use App\Api\Controller\ApiController;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/agent/details")
 */
class AgentApiController extends ApiController
{
    /**
     * @Route("/organization/{agent}", methods={"GET"}, name="api_agent_organization_details")
     * @ParamConverter("agent", options={"mapping": {"agent": "slug"}})
     */
    public function agentOrganizationDetails(Organization $agent): Response
    {
        return new JsonResponse((new OrganizationApiResource($agent))->toArray());
    }

    /**
     * @Route("/person/{agent}", methods={"GET"}, name="api_agent_person_details")
     * @ParamConverter("agent", options={"mapping": {"agent": "slug"}})
     */
    public function agentPersonDetails(Person $agent): Response
    {
        return new JsonResponse((new PersonApiResource($agent))->toArray());
    }
}
