<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentController extends FAIRDataController
{
    /**
     * @Route("/agent/person/{person}", name="agent_person")
     * @ParamConverter("person", options={"mapping": {"person": "slug"}})
     */
    public function person(Person $person): Response
    {
        return new Response(
            (new PersonGraphResource($person))->toGraph($this->baseUri)->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @Route("/agent/organization/{organization}", name="agent_organization")
     * @ParamConverter("organization", options={"mapping": {"organization": "slug"}})
     */
    public function organization(Organization $organization): Response
    {
        return new Response(
            (new OrganizationGraphResource($organization))->toGraph($this->baseUri)->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
