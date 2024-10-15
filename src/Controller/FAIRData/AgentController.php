<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentController extends FAIRDataController
{
    #[Route(path: '/fdp/person/{person}', name: 'agent_person')]
    public function person(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['person' => 'slug'])]
    Person $person): Response
    {
//        if ($this->acceptsHttp($request)) {
//            return $this->render('react.html.twig', $this->getAgentSeoTexts($person));
//        }

        return new Response(
            (new PersonGraphResource($person, $this->basePurl))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    #[Route(path: '/fdp/organization/{organization}', name: 'agent_organization')]
    public function organization(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['organization' => 'slug'])]
    Organization $organization): Response
    {
//        if ($this->acceptsHttp($request)) {
//            return $this->render('react.html.twig', $this->getAgentSeoTexts($organization));
//        }

        return new Response(
            (new OrganizationGraphResource($organization, $this->basePurl))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
