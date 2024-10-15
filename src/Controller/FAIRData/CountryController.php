<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Country;
use App\Graph\Resource\CountryGraphResource;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends FAIRDataController
{
    #[Route(path: '/fdp/country/{country}', name: 'agent_person')]
    public function country(
        #[MapEntity(mapping: ['country' => 'code'])]
        Country $country,
    ): Response {
        return new Response(
            (new CountryGraphResource($country, $this->basePurl))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
