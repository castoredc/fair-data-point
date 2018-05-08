<?php

namespace App\Controller;

use App\Model\ApiClient;
use EasyRdf_Namespace;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RDFRendererController extends Controller
{
    private $studyId = '57051B03-59C1-23A3-3ADA-7AA791481606';
    private $secret = '25e3956844bb52ea99c4230c139a3f67';
    private $clientId = '34413D8C-05D9-A974-F3ED-654A2EBE2FDC';
    #private $url = 'https://vasca.pilot.castoredc.com';
    private $url = 'http://127.0.0.1:8000';

    private $studies = [
        'radboud' => '57051B03-59C1-23A3-3ADA-7AA791481606'
    ];

    public function __construct()
    {
        EasyRdf_Namespace::set('r3d', 'http://www.re3data.org/schema/3-0#');
    }

    /**
     * @Route("/fdp", name="fdp_render")
     */
    public function fdpAction()
    {
        $this->url .= '/fdp';

        $graph = new \EasyRdf_Graph();

        $graph->addLiteral($this->url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($this->url, 'dcterms:hasVersion', '0.1');
        $graph->addLiteral($this->url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($this->url, 'dcterms:publisher', 'https://orcid.org/0000-0001-9217-278X');
        $graph->addResource($this->url, 'dcterms:publisher', 'https://www.radboudumc.nl/patientenzorg');

        $graph->addResource($this->url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($this->url, 'dcterms:license', 'TBD');

        $graph->addResource($this->url, 'http://www.re3data.org/schema/3-0#dataCatalog', $this->url . '/vasca');

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}", name="catalog_render")
     */
    public function catalogAction($catalog)
    {
        $this->url .= '/fdp/' . $catalog;

        $graph = new \EasyRdf_Graph();

        $graph->addLiteral($this->url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($this->url, 'dcterms:hasVersion', '0.1');
        $graph->addLiteral($this->url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($this->url, 'dcterms:publisher', 'https://orcid.org/0000-0001-9217-278X');
        $graph->addResource($this->url, 'dcterms:publisher', 'https://www.radboudumc.nl/patientenzorg');

        $graph->addResource($this->url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($this->url, 'dcterms:license', 'TBD');

        foreach($this->studies as $study) {
            $graph->addResource($this->url, 'dcat:dataset', $this->url . '/' . $study);
        }

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}/{study}", name="study_render")
     */
    public function studyAction($catalog, $study)
    {
        $this->url .= '/fdp/' . $catalog . '/' . $study;

        $apiClient = new ApiClient($useCache = false);
        $apiClient->auth($this->clientId, $this->secret);
        $study = $apiClient->getStudy($study);

        $graph = new \EasyRdf_Graph();

        $graph->addLiteral($this->url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($this->url, 'dcterms:hasVersion', $study['version']);
        $graph->addLiteral($this->url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($this->url, 'dcterms:publisher', 'https://orcid.org/0000-0001-9217-278X');
        $graph->addResource($this->url, 'dcterms:publisher', 'https://www.radboudumc.nl/patientenzorg');

        $graph->addResource($this->url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($this->url, 'dcterms:license', 'TBD');
        $graph->addResource($this->url, 'dcat:theme', 'http://www.wikidata.org/entity/Q7916449');
        $graph->addResource($this->url, 'dcat:distribution', $this->url . '/distribution');

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/{catalog}/{study}/distribution", name="distribution_render")
     */
    public function distributionAction($catalog, $study)
    {
        $this->url .= '/fdp/' . $catalog . '/' . $study. '/distribution';

        $apiClient = new ApiClient($useCache = false);
        $apiClient->auth($this->clientId, $this->secret);
        $study = $apiClient->getStudy($study);

        $graph = new \EasyRdf_Graph();

        $graph->addLiteral($this->url, 'dcterms:title', 'Registry of vascular anomalies');
        $graph->addLiteral($this->url, 'dcterms:hasVersion', $study['version']);
        $graph->addLiteral($this->url, 'dcterms:description', 'Databases of the ERN vascular anomalies');

        $graph->addResource($this->url, 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');
        $graph->addResource($this->url, 'dcterms:license', 'TBD');
        $graph->addResource($this->url, 'a', 'dcat:Distribution');

        $graph->addResource($this->url, 'dcat:accessURL', $this->url . '/rdf');
        $graph->addLiteral($this->url, 'dcat:mediaType', 'text/turtle');

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }


    /**
     * @Route("/fdp/{catalog}/{study}/distribution/rdf", name="rdf_render")
     */
    public function rdfAction($catalog, $study)
    {
        $this->url .= '/fdp/' . $catalog . '/' . $study;

        $apiClient = new ApiClient($useCache = false);
        $apiClient->auth($this->clientId, $this->secret);
        $records = $apiClient->getRecords($this->studyId);

        $templateData = [
            'records' => []
        ];

        foreach ($records as $record) {
            if ($record['archived']) {
                continue;
            }
            $values = $apiClient->getRecordDataPoints($this->studyId, $record['record_id']);
            $fieldValues = [];

            foreach ($values as $value) {
                $fieldValues[$value['field_id']] = $value['field_value'];
            }

            $templateData['records'][] = [
                'record_id' => $record['record_id'],
                'rd_diagnosis_orpha' => $fieldValues['8321A9FE-D4F7-2D81-580C-4E6424D818C0'] ?? 'unknown',
                'date_of_birth' => $fieldValues['B9768A6C-DB47-C614-0953-486962582374'] ?? 'unknown',
                'whodas_score' => $fieldValues['56E1641F-65D0-DBA3-D28A-391A9850B4F7'] ?? 'unknown',
            ];

            $templateData['castor'] = [
                'uri' => $this->url
            ];
        }


        return $this->render('rdf-list.html.twig', $templateData,
            new Response(
                'Content',
                Response::HTTP_OK,
                array('content-type' => 'text/turtle')
            ));
    }
    
}
