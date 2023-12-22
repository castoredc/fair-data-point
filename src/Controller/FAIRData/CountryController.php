<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Country;
use App\Graph\Resource\CountryGraphResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends FAIRDataController
{
    /**
     * @Route("/fdp/country/{country}", name="agent_person")
     * @ParamConverter("country", options={"mapping": {"country": "code"}})
     */
    public function country(Country $country): Response
    {
        return new Response(
            (new CountryGraphResource($country, $this->basePurl))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
