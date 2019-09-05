<?php

namespace App\Controller;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Iri;
use App\Entity\RdfItem;
use App\Model\ApiClient;
use App\Service\CastorAuth;
use EasyRdf_Graph;
use EasyRdf_Namespace;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RDFRendererController extends Controller
{
    const ACCEPT_HTTP = 1;
    const ACCEPT_JSON = 2;
    const ACCEPT_TURTLE = 3;

    /**
     * @var ApiClient
     */
    private $apiClient;

    public function __construct(CastorAuth $castorAuth, CastorAuth\RouteParametersStorage $routeParametersStorage)
    {
        $this->apiClient = new ApiClient();
        $this->apiClient->auth(getenv('CASTOR_OAUTH_CLIENT_ID'), getenv('CASTOR_OAUTH_CLIENT_SECRET'));
    }

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

    private function graphToObject(EasyRdf_Graph $graph)
    {
        $graphArray = $graph->toRdfPhp();
        $graphArray = reset($graphArray);

        $rdfItems = [];

        foreach($graphArray as $iri => $children)
        {
            $short = EasyRdf_Namespace::shorten($iri);
            $rdfItem = new RdfItem(new Iri($iri), $short);
            $rdfItem->childrenFromData($children);

            $rdfItems[] = $rdfItem->toArray();
        }

        return $rdfItems;
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
                    'fdp' => $fdp->toJson()
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

        if($accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($accept == self::ACCEPT_JSON) {
            return new JsonResponse(
                [
                    'success' => true,
                    'catalog' => $catalog->toArray()
                ]
            );
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
        $catalog = $this->fdp->getCatalog($catalogSlug);
        $dataset = $catalog->getDataset($datasetSlug);

        $graph = $dataset->toGraph();

        if($this->accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($this->accept == self::ACCEPT_JSON) {
            $rdfItems = $this->graphToObject($graph);
            $label = $graph->getLiteral($dataset->getIri()->getValue(), 'rdfs:label')->getValue();

            return new JsonResponse(
                [
                    'success' => true,
                    'graph' => $rdfItems,
                    'label' => $label,
                    'turtle' => $graph->serialise('turtle')
                ]
            );
        }

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/distribution", name="distribution_render")
     * @param Request $request
     * @param $catalogSlug
     * @param $datasetSlug
     * @return Response
     */
    public function distributionAction(Request $request, $catalogSlug, $datasetSlug)
    {
        $catalog = $this->fdp->getCatalog($catalogSlug);
        $dataset = $catalog->getDataset($datasetSlug);
        $distribution = $dataset->getDistribution();

        $graph = $distribution->toGraph();

        if($this->accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($this->accept == self::ACCEPT_JSON) {
            $rdfItems = $this->graphToObject($graph);
            $label = $graph->getLiteral($distribution->getIri()->getValue(), 'rdfs:label')->getValue();

            return new JsonResponse(
                [
                    'success' => true,
                    'graph' => $rdfItems,
                    'label' => $label,
                    'turtle' => $graph->serialise('turtle')
                ]
            );
        }

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @param ApiClient $apiClient
     * @param $studyId
     * @param $recordId
     * @param $fields
     * @param $fieldVariables
     * @param $metadatas
     * @return array
     */
    private function getRecord(ApiClient $apiClient, $studyId, $recordId, $fields, $fieldVariables, $metadatas)
    {
        $values = $this->apiClient->getRecordDataPoints($studyId, $recordId);
        $fieldValues = [
            'record_id' => $recordId
        ];

        foreach ($values as $value) {
            $fieldId = $value['field_id'];
            $fieldVariable = $fieldVariables[$value['field_id']];

            if(in_array($fields[$fieldId]['field_type'], $this->optionGroupFields) && isset($metadatas[$fieldId]) && isset($metadatas[$fieldId][$value['field_value']]))
            {
                $fieldValues[$fieldVariable] = $metadatas[$fieldId][$value['field_value']][$this->metadataName];
            }
            else
            {
                $fieldValues[$fieldVariable] = $value['field_value'];
            }
        }

        return $fieldValues;
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/distribution/rdf", name="rdf_render")
     * @param Request $request
     * @param $catalogSlug
     * @param $datasetSlug
     * @return Response
     */
    public function rdfAction(Request $request, $catalogSlug, $datasetSlug)
    {
        $catalog = $this->fdp->getCatalog($catalogSlug);
        $dataset = $catalog->getDataset($datasetSlug);
        $distribution = $dataset->getDistribution();

//        // Uncomment lines below to enable authentication
//        if (($result = $this->checkRouteAccessWithOauth(
//            $request,
//            'rdf_render',
//            ['catalog' => $catalog, 'study' => $study]
//            )) !== true) {
//            return $result;
//        }

        $url = $distribution->getIri() . '/rdf';
        $study = $dataset->getStudyId();

        $metadatas = $this->apiClient->getMetadata($study);
        $apiFields = $this->apiClient->getFields($study);
        $records = $this->apiClient->getRecords($study);

        $fields = [];
        $fieldVariables = [];
        foreach($apiFields as $field) {
            $fieldVariables[$field['id']] = $field['field_variable_name'];
            $fields[$field['id']] = $field;
        }

        $templateData = [
            'records' => []
        ];

        $i = 0;
        foreach ($records as $record) {
            if ($record['archived']) {
                continue;
            }

            $data = $this->getRecord($this->apiClient, $study, $record['record_id'], $fields, $fieldVariables, $metadatas);

            if(isset($data['informed_consent']) && $data['informed_consent'] == 'Yes')
            {
                $templateData['records'][] = $data;
                $i++;
            }

            if($i == 50) break;
        }

        $templateData['castor'] = [
            'uri' => $url
        ];

        $content = $this->renderView('rdf-list.html.twig', $templateData);
        $trimmedContent = trim(preg_replace('/^  |\G  /m', '', $content));

        if($request->query->has('download') && $request->query->get('download') == true)
        {
            $response = new Response($trimmedContent);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $study . '_' . time() . '.ttl'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return new Response(
            $trimmedContent,
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}/{study}/distribution/rdf/{record}", name="rdf_record_render")
     * @param Request $request
     * @param $catalog
     * @param $study
     * @param $record
     * @return Response
     */
    public function rdfRecordAction(Request $request, $catalog, $study, $record)
    {
//        // Uncomment lines below to enable authentication
//        if (($result = $this->checkRouteAccessWithOauth(
//            $request,
//            'rdf_render',
//            ['catalog' => $catalog, 'study' => $study]
//            )) !== true) {
//            return $result;
//        }

        $url = getenv('FDP_URL') . '/fdp/' . $catalog . '/' . $study . '/distribution/rdf';

        $metadatas = $this->apiClient->getMetadata($study);
        $apiFields = $this->apiClient->getFields($study);
        $records = $this->apiClient->getRecords($study);

        $fields = [];
        $fieldVariables = [];
        foreach($apiFields as $field) {
            $fieldVariables[$field['id']] = $field['field_variable_name'];
            $fields[$field['id']] = $field;
        }

        if(!isset($records[$record]))
        {
            throw new NotFoundHttpException('The record you are trying to open, does not exist.');
        }
        if ($records[$record]['archived']) {
            throw new NotFoundHttpException('The record you are trying to open is archived.');
        }

        $templateData['record'] = $this->getRecord($this->apiClient, $study, $record, $fields, $fieldVariables, $metadatas);
        $templateData['castor'] = [
            'uri' => $url
        ];


        $content = $this->renderView('rdf.html.twig', $templateData);
        $trimmedContent = trim(preg_replace('/^  |\G  /m', '', $content));

        if($request->query->has('download') && $request->query->get('download') == true)
        {
            $response = new Response($trimmedContent);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $study . '_' . $record . '_' . time() . '.ttl'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return new Response(
            $trimmedContent,
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );

    }
    
}
