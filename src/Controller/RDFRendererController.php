<?php

namespace App\Controller;

use App\Entity\Catalog;
use App\Entity\FAIRDataPoint;
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
    private $metadataName = 'SNOMED';

    /** @var CastorAuth */
    private $authenticator;

    /**
     * @var CastorAuth\RouteParametersStorage
     */
    private $routeStorage;

    private $studies = [
        'test' => '57051B03-59C1-23A3-3ADA-7AA791481606',
        'radboudumc' => '13AD6C43-0CA0-C51F-7EB5-32DCC237C87E'
        //'demo' => 'A2FB2912-A347-1839-2940-EE686FC5A5D3'
    ];

    private $optionGroupFields = [
        'radio',
        'dropdown',
        'checkbox'
    ];

    /** @var FAIRDataPoint */
    private $fdp;

    /** @var int */
    private $accept;

    const ACCEPT_HTTP = 1;
    const ACCEPT_JSON = 2;
    const ACCEPT_TURTLE = 3;

    /**
     * @var ApiClient
     */
    private $apiClient;

    public function __construct(CastorAuth $castorAuth, CastorAuth\RouteParametersStorage $routeParametersStorage)
    {
        $this->authenticator = $castorAuth;
        $this->routeStorage = $routeParametersStorage;


        $this->apiClient = new ApiClient();
        $this->apiClient->auth(getenv('CASTOR_OAUTH_CLIENT_ID'), getenv('CASTOR_OAUTH_CLIENT_SECRET'));

        $this->detectAccept();
        $this->populateVascaData();

        EasyRdf_Namespace::set('r3d', 'http://www.re3data.org/schema/3-0#');
    }

    private function populateVascaData()
    {
        $this->fdp = new FAIRDataPoint(
            new Iri(getenv('FDP_URL') . '/fdp'),
            'Castor EDC FAIR Data Point',
            '0.2',
            'FAIR Data Point (FDP) of Castor EDC',
            [
                new Iri('https://www.castoredc.com')
            ],
            new Iri('http://id.loc.gov/vocabulary/iso639-1/en'),
            null,
            null,
            null,
            null
        );

        $this->fdp->addCatalog(
            'vasca',
            'Registry of vascular anomalies',
            '0.2',
            'Databases of the ERN vascular anomalies',
            [
                new Iri('https://orcid.org/0000-0001-9217-278X'),
                new Iri('https://www.radboudumc.nl/patientenzorg')
            ],
            new Iri('http://id.loc.gov/vocabulary/iso639-1/en'),
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            new iri('http://dbpedia.org/resource/Vascular_anomaly')
        );

        $this->fdp->getCatalog('vasca')->addDataset(
            'test',
            '57051B03-59C1-23A3-3ADA-7AA791481606',
            'Registry of vascular anomalies - Test dataset',
            '0.2',
            'Test dataset of the ERN vascular anomalies',
            [
                new Iri('https://orcid.org/0000-0001-9217-278X'),
                new Iri('https://www.radboudumc.nl/patientenzorg')
            ],
            new Iri('http://id.loc.gov/vocabulary/iso639-1/en'),
            null,
            null,
            null,
            null,
            null,
            null,
            new Iri('http://www.wikidata.org/entity/Q7916449'),
            null,
            null,
            null
        );

        $this->fdp->getCatalog('vasca')->getDataset('test')->addDistribution(
            'Registry of vascular anomalies - Test distribution',
            '0.2',
            'Test distribution of the ERN vascular anomalies',
            [
                new Iri('https://orcid.org/0000-0001-9217-278X'),
                new Iri('https://www.radboudumc.nl/patientenzorg')
            ],
            new Iri('http://id.loc.gov/vocabulary/iso639-1/en'),
            null,
            null,
            null,
            null,
            null
        );
    }


    private function checkRouteAccessWithOauth(Request $request, $routeName, $routeParameters = [])
    {
        if (!$this->authenticator->isTokenValid($request->get('token'))) {
            $this->routeStorage->setRouteParameters(new CastorAuth\RouteParameters($routeName, $routeParameters));
            return $this->redirect($this->authenticator->getAuthorizationUrl());
        }

        return true;
    }

    private function detectAccept()
    {
        $types = explode(',', $_SERVER['HTTP_ACCEPT']);

        $this->accept = self::ACCEPT_TURTLE;
        if(in_array('text/html', $types)) $this->accept = self::ACCEPT_HTTP;
        if(in_array('application/json', $types)) $this->accept = self::ACCEPT_JSON;
        if(in_array('text/turtle', $types)) $this->accept = self::ACCEPT_TURTLE;
        if(in_array('text/turtle;q=0.8', $types)) $this->accept = self::ACCEPT_TURTLE;
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
        $graph = $this->fdp->toGraph();

        if($this->accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($this->accept == self::ACCEPT_JSON) {
            $rdfItems = $this->graphToObject($graph);

            $label = $graph->getLiteral($this->fdp->getIri()->getValue(), 'rdfs:label')->getValue();

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
     * @Route("/fdp/{catalogSlug}", name="catalog_render")
     * @param Request $request
     * @param $catalogSlug
     * @return Response
     */
    public function catalogAction(Request $request, $catalogSlug)
    {
        $catalog = $this->fdp->getCatalog($catalogSlug);
        $graph = $catalog->toGraph();

        if($this->accept == self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }
        else if($this->accept == self::ACCEPT_JSON) {
            $rdfItems = $this->graphToObject($graph);
            $label = $graph->getLiteral($catalog->getIri()->getValue(), 'rdfs:label')->getValue();

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
