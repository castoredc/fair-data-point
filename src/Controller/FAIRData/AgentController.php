<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentController extends FAIRDataController
{
    /**
     * @Route("/fdp/person/{person}", name="agent_person")
     * @ParamConverter("person", options={"mapping": {"person": "slug"}})
     */
    public function person(Person $person, Request $request): Response
    {
        if ($this->acceptsHttp($request)) {
            return $this->render('react.html.twig', $this->getAgentSeoTexts($person));
        }

        return new Response(
            (new PersonGraphResource($person, $this->basePurl))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @Route("/fdp/organization/{organization}", name="agent_organization")
     * @ParamConverter("organization", options={"mapping": {"organization": "slug"}})
     */
    public function organization(Organization $organization, Request $request): Response
    {
        if ($this->acceptsHttp($request)) {
            return $this->render('react.html.twig', $this->getAgentSeoTexts($organization));
        }

        return new Response(
            (new OrganizationGraphResource($organization, $this->basePurl))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
