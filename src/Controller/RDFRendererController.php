<?php

namespace App\Controller;

use App\Model\ApiClient;
use App\Service\CastorAuth;
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
        'radboud' => '57051B03-59C1-23A3-3ADA-7AA791481606'
    ];

    private $optionGroupFields = [
        'radio',
        'dropdown',
        'checkbox'
    ];

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

        EasyRdf_Namespace::set('r3d', 'http://www.re3data.org/schema/3-0#');
    }

    private function checkRouteAccessWithOauth(Request $request, $routeName, $routeParameters = [])
    {
        if (!$this->authenticator->isTokenValid($request->get('token'))) {
            $this->routeStorage->setRouteParameters(new CastorAuth\RouteParameters($routeName, $routeParameters));
            return $this->redirect($this->authenticator->getAuthorizationUrl());
        }

        return true;
    }

    /**
     * @Route("/test/{name}", name="test")
     * @param Request $request
     * @return string
     */
    /* public function authTestAction(Request $request, $name)
    {
        if (($result = $this->checkRouteAccessWithOauth($request, 'test', ['name' => $name])) !== true) {
            return $result;
        }
        return new JsonResponse(['Hello ' . $name]);
    } */


    /**
     * @Route("/fdp", name="fdp_render")
     * @param Request $request
     * @return Response
     */
    public function fdpAction(Request $request)
    {
        $url =  getenv('FDP_URL') . '/fdp';

        $graph = new \EasyRdf_Graph();

        $graph->addResource($url, 'a', 'r3d:Repository');

        $graph->addLiteral($url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($url, 'dcterms:hasVersion', '0.1');
        $graph->addLiteral($url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($url, 'dcterms:publisher', 'https://orcid.org/0000-0001-9217-278X');
        $graph->addResource($url, 'dcterms:publisher', 'https://www.radboudumc.nl/patientenzorg');

        $graph->addResource($url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($url, 'dcterms:license', 'TBD');

        $graph->addResource($url, 'http://www.re3data.org/schema/3-0#dataCatalog', $url . '/vasca');

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}", name="catalog_render")
     * @param Request $request
     * @param $catalog
     * @return Response
     */
    public function catalogAction(Request $request, $catalog)
    {
        $url = getenv('FDP_URL') . '/fdp/' . $catalog;

        $graph = new \EasyRdf_Graph();

        $graph->addResource($url, 'a', 'dcat:Catalog');

        $graph->addLiteral($url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($url, 'dcterms:hasVersion', '0.1');
        $graph->addLiteral($url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($url, 'dcterms:publisher', 'https://orcid.org/0000-0001-9217-278X');
        $graph->addResource($url, 'dcterms:publisher', 'https://www.radboudumc.nl/patientenzorg');

        $graph->addResource($url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($url, 'dcterms:license', 'TBD');

        foreach($this->studies as $study) {
            $graph->addResource($url, 'dcat:dataset', $url . '/' . $study);
        }

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}/{study}", name="dataset_render")
     * @param Request $request
     * @param $catalog
     * @param $study
     * @return Response
     */
    public function datasetAction(Request $request, $catalog, $study)
    {
        $url = getenv('FDP_URL') . '/fdp/' . $catalog . '/' . $study;

        $study = $this->apiClient->getStudy($study);

        $graph = new \EasyRdf_Graph();

        $graph->addResource($url, 'a', 'dcat:Dataset');

        $graph->addLiteral($url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($url, 'dcterms:hasVersion', $study['version']);
        $graph->addLiteral($url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($url, 'dcterms:publisher', 'https://orcid.org/0000-0001-9217-278X');
        $graph->addResource($url, 'dcterms:publisher', 'https://www.radboudumc.nl/patientenzorg');

        $graph->addResource($url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($url, 'dcterms:license', 'TBD');
        $graph->addResource($url, 'dcat:theme', 'http://www.wikidata.org/entity/Q7916449');
        $graph->addResource($url, 'dcat:distribution', $url . '/distribution');

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}/{study}/distribution", name="distribution_render")
     * @param Request $request
     * @param $catalog
     * @param $study
     * @return Response
     */
    public function distributionAction(Request $request, $catalog, $study)
    {
        $url = getenv('FDP_URL') . '/fdp/' . $catalog . '/' . $study. '/distribution';

        
        $study = $this->apiClient->getStudy($study);

        $graph = new \EasyRdf_Graph();

        $graph->addResource($url, 'a', 'dcat:Distribution');

        $graph->addLiteral($url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($url, 'dcterms:hasVersion', $study['version']);
        $graph->addLiteral($url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($url, 'dcterms:license', 'TBD');

        $graph->addResource($url, 'dcat:downloadURL', $url . '/rdf?download=1');
        $graph->addResource($url, 'dcat:accessURL', $url . '/rdf');
        $graph->addLiteral($url, 'dcat:mediaType', 'text/turtle');

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
     * @Route("/fdp/{catalog}/{study}/distribution/rdf", name="rdf_render")
     * @param Request $request
     * @param $catalog
     * @param $study
     * @return Response
     */
    public function rdfAction(Request $request, $catalog, $study)
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

        $templateData = [
            'records' => []
        ];

        foreach ($records as $record) {
            if ($record['archived']) {
                continue;
            }

            $templateData['records'][] = $this->getRecord($this->apiClient, $study, $record['record_id'], $fields, $fieldVariables, $metadatas);
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
