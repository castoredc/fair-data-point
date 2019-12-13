<?php

namespace App\Controller;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Distribution\RDFDistribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use App\Entity\RdfItem;
use App\Helper\RDFTwigRenderHelper;
use App\Model\Castor\ApiClient;
use EasyRdf_Graph;
use EasyRdf_Namespace;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RDFRendererController extends Controller
{
    const ACCEPT_HTTP = 1;
    const ACCEPT_JSON = 2;
    const ACCEPT_TURTLE = 3;

    private function detectAccept(Request $request)
    {
        if($request->get('format') != null)
        {
            $format = $request->get('format');

            if($format == 'html')  return self::ACCEPT_HTTP;
            if($format == 'json')  return self::ACCEPT_JSON;
            if($format == 'ttl') return self::ACCEPT_TURTLE;
        }

        $types = $request->getAcceptableContentTypes();

        if(in_array('text/html', $types)) return self::ACCEPT_HTTP;
        if(in_array('application/json', $types)) return self::ACCEPT_JSON;
        if(in_array('text/turtle', $types)) return self::ACCEPT_TURTLE;
        if(in_array('text/turtle;q=0.8', $types)) return self::ACCEPT_TURTLE;

        return self::ACCEPT_TURTLE;
    }

    /**
     * @Route("/fdp", name="fdp_render")
     * @param Request $request
     * @return Response
     */
    public function fdpAction(Request $request)
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $fairDataPointRepository->findOneBy(["iri" => $uri]);

        if(!$fdp) throw new NotFoundHttpException("FAIR Data Point not found");

        if($accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($accept == self::ACCEPT_JSON) {
            return new JsonResponse(
                [
                    'success' => true,
                    'fdp' => $fdp->toArray()
                ]
            );
        }

        $graph = $fdp->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/agent/{agentType}/{agentSlug}", name="agent_render")
     * @param Request $request
     * @param $agentType
     * @param $agentSlug
     * @return Response
     */
    public function profileAction(Request $request, $agentType, $agentSlug)
    {
        $accept = $this->detectAccept($request);

        $doctrine = $this->getDoctrine();
        $personRepository = $doctrine->getRepository(Person::class);
        $organizationRepository = $doctrine->getRepository(Organization::class);

        /** @var Agent $contact */
        $contact = null;
        if($agentType == "person") $contact = $personRepository->findOneBy(["slug" => $agentSlug]);
        if($agentType == "organization") $contact = $organizationRepository->findOneBy(["slug" => $agentSlug]);

        if(!$contact) throw new NotFoundHttpException("Agent not found");

        if($accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($accept == self::ACCEPT_JSON) {
            return new JsonResponse(
                [
                    'success' => true,
                    'agent' => $contact->toArray()
                ]
            );
        }

        $graph = $contact->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}", name="catalog_render")
     * @param Request $request
     * @param $catalogSlug
     * @return Response
     */
    public function catalogAction(Request $request, $catalogSlug)
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $fairDataPointRepository->findOneBy(["iri" => $uri]);
        if(!$fdp) throw new NotFoundHttpException("FAIR Data Point not found");

        /** @var Catalog $catalog */
        $catalog = $catalogRepository->findOneBy(["slug" => $catalogSlug, "fairDataPoint" => $fdp]);
        if(!$catalog) throw new NotFoundHttpException("Catalog not found");

        if($accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($accept == self::ACCEPT_JSON) {
            $response = [
                'success' => true,
                'catalog' => $catalog->toArray()
            ];

            if($request->get('ui') != null) $response['fdp'] = $fdp->toBasicArray();

            return new JsonResponse($response);
        }
        $graph = $catalog->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}", name="dataset_render")
     * @param Request $request
     * @param $catalogSlug
     * @param $datasetSlug
     * @return Response
     */
    public function datasetAction(Request $request, $catalogSlug, $datasetSlug)
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $fairDataPointRepository->findOneBy(["iri" => $uri]);
        if(!$fdp) throw new NotFoundHttpException("FAIR Data Point not found");

        /** @var Catalog $catalog */
        $catalog = $catalogRepository->findOneBy(["slug" => $catalogSlug, "fairDataPoint" => $fdp]);
        if(!$catalog) throw new NotFoundHttpException("Catalog not found");

        /** @var Dataset $dataset */
        $dataset = $datasetRepository->findOneBy(["slug" => $datasetSlug]);
        if(!$dataset && !$dataset->hasCatalog($catalog)) throw new NotFoundHttpException("Dataset not found");

        if($accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($accept == self::ACCEPT_JSON) {
            $response = [
                'success' => true,
                'dataset' => $dataset->toArray(),
            ];

            if($request->get('ui') != null) $response['catalog'] = $catalog->toBasicArray();

            return new JsonResponse($response);
        }
        $graph = $dataset->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/{distributionSlug}", name="distribution_render")
     * @param Request $request
     * @param $catalogSlug
     * @param $datasetSlug
     * @param $distributionSlug
     * @return Response
     */
    public function distributionAction(Request $request, $catalogSlug, $datasetSlug, $distributionSlug)
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);
        $distributionRepository = $doctrine->getRepository(Distribution::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $fairDataPointRepository->findOneBy(["iri" => $uri]);
        if(!$fdp) throw new NotFoundHttpException("FAIR Data Point not found");

        /** @var Catalog $catalog */
        $catalog = $catalogRepository->findOneBy(["slug" => $catalogSlug, "fairDataPoint" => $fdp]);
        if(!$catalog) throw new NotFoundHttpException("Catalog not found");

        /** @var Dataset $dataset */
        $dataset = $datasetRepository->findOneBy(["slug" => $datasetSlug]);
        if(!$dataset && !$dataset->hasCatalog($catalog)) throw new NotFoundHttpException("Dataset not found");

        /** @var Distribution $distribution */
        $distribution = $distributionRepository->findOneBy(["slug" => $distributionSlug, "dataset" => $dataset]);
        if(!$distribution) throw new NotFoundHttpException("Distribution not found");

        if($accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($accept == self::ACCEPT_JSON) {
            $response = [
                'success' => true,
                'distribution' => $distribution->toArray(),
            ];

            if($request->get('ui') != null) $response['dataset'] = $dataset->toBasicArray();

            return new JsonResponse($response);
        }

        $graph = $distribution->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/{distributionSlug}/rdf", name="rdf_render")
     * @param Request $request
     * @param $catalogSlug
     * @param $datasetSlug
     * @param $distributionSlug
     * @return Response
     * @throws Exception
     */
    public function rdfAction(Request $request, $catalogSlug, $datasetSlug, $distributionSlug)
    {
        if(!$this->getUser())
        {
            return $this->redirect('/connect/castor?target_path=' . $request->getRequestUri());
        }

        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);
        $distributionRepository = $doctrine->getRepository(Distribution::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $fairDataPointRepository->findOneBy(["iri" => $uri]);
        if(!$fdp) throw new NotFoundHttpException("FAIR Data Point not found");

        /** @var Catalog $catalog */
        $catalog = $catalogRepository->findOneBy(["slug" => $catalogSlug, "fairDataPoint" => $fdp]);
        if(!$catalog) throw new NotFoundHttpException("Catalog not found");

        /** @var Dataset $dataset */
        $dataset = $datasetRepository->findOneBy(["slug" => $datasetSlug]);
        if(!$dataset && !$dataset->hasCatalog($catalog)) throw new NotFoundHttpException("Dataset not found");

        $client = new ApiClient();
        $client->setToken($this->getUser()->getToken());

        /** @var RDFDistribution $distribution */
        $distribution = $distributionRepository->findOneBy(["slug" => $distributionSlug, "dataset" => $dataset]);
        if(!$distribution) throw new NotFoundHttpException("Distribution not found");
        if(!$distribution instanceof RDFDistribution) throw new NotFoundHttpException("This distribution is not a RDF distribution");

        try
        {
            $study = $client->getStudy($dataset->getStudy()->getId());
        }
        catch(UnauthorizedHttpException $e)
        {
            throw new UnauthorizedHttpException('', "You do not have permission to access this study");
        }

        $helper = new RDFTwigRenderHelper($client, $study, $this->get('twig'), $distribution);

        if($request->query->has('download') && $request->query->get('download') == true)
        {
            $response = new Response( $helper->renderRecords());
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $study->getSlug() . '_' . time() . '.ttl'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return new Response(
            $helper->renderRecords(),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/{distributionSlug}/rdf/{recordId}", name="rdf_render_record")
     * @param Request $request
     * @param $catalogSlug
     * @param $datasetSlug
     * @param $distributionSlug
     * @param $recordId
     * @return Response
     * @throws Exception
     */
    public function rdfRecordAction(Request $request, $catalogSlug, $datasetSlug, $distributionSlug, $recordId)
    {
        if(!$this->getUser())
        {
            return $this->redirect('/connect/castor?target_path=' . $request->getRequestUri());
        }

        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);
        $distributionRepository = $doctrine->getRepository(Distribution::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $fairDataPointRepository->findOneBy(["iri" => $uri]);
        if(!$fdp) throw new NotFoundHttpException("FAIR Data Point not found");

        /** @var Catalog $catalog */
        $catalog = $catalogRepository->findOneBy(["slug" => $catalogSlug, "fairDataPoint" => $fdp]);
        if(!$catalog) throw new NotFoundHttpException("Catalog not found");

        /** @var Dataset $dataset */
        $dataset = $datasetRepository->findOneBy(["slug" => $datasetSlug]);
        if(!$dataset && !$dataset->hasCatalog($catalog)) throw new NotFoundHttpException("Dataset not found");

        $client = new ApiClient();
        $client->setToken($this->getUser()->getToken());

        /** @var RDFDistribution $distribution */
        $distribution = $distributionRepository->findOneBy(["slug" => $distributionSlug, "dataset" => $dataset]);
        if(!$distribution) throw new NotFoundHttpException("Distribution not found");
        if(!$distribution instanceof RDFDistribution) throw new NotFoundHttpException("This distribution is not a RDF distribution");

        try
        {
            $study = $client->getStudy($dataset->getStudy()->getId());
        }
        catch(UnauthorizedHttpException $e)
        {
            throw new UnauthorizedHttpException('', "You do not have permission to access this study");
        }

        $helper = new RDFTwigRenderHelper($client, $study, $this->get('twig'), $distribution);

        if($request->query->has('download') && $request->query->get('download') == true)
        {
            $response = new Response($helper->renderRecord($recordId));
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $study->getSlug() . '_' . $recordId . '_' . time() . '.ttl'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return new Response(
            $helper->renderRecord($recordId),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }
    
}
