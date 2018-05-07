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
    private $url = 'vasca.pilot.castoredc.com';

    /**
     * @Route("/fdp", name="fdp_render")
     */
    public function fdpAction()
    {
        $graph = new \EasyRdf_Graph();
        EasyRdf_Namespace::set('r3d', 'http://www.re3data.org/schema/3-0#');

        $graph->addLiteral('https://' . $this->url . '/fdp', 'dcterms:title', 'Castor EDC VASCA FAIR Data Point');
        $graph->addLiteral('https://' . $this->url . '/fdp', 'dcterms:identifier', 'CastorEDC-VASCA');
        $graph->addLiteral('https://' . $this->url . '/fdp', 'dcterms:hasVersion', '0.1');
        $graph->addLiteral(
            'https://' . $this->url . '/fdp',
            'dcterms:description',
            'Lorum ipsum dolar si amet.'
        );
        $graph->addResource('https://' . $this->url . '/fdp', 'dcterms:publisher', 'https://vascern.eu/');
        $graph->addResource(
            'https://' . $this->url . '/fdp',
            'dcterms:language',
            'http://id.loc.gov/vocabulary/iso639-1/en'
        );
        $graph->addResource(
            'https://' . $this->url . '/fdp',
            'dcterms:license',
            'TBD'
        );
        $graph->addResource(
            'https://' . $this->url . '/fdp',
            'http://www.re3data.org/schema/3-0#dataCatalog',
            'https://' . $this->url . '/fdp/vasca'
        );

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/fdp/vasca", name="fdp_vasca_render")
     */
    public function vascaAction()
    {
        $graph = new \EasyRdf_Graph();
        EasyRdf_Namespace::set('r3d', 'http://www.re3data.org/schema/3-0#');

        $graph->addLiteral('https://' . $this->url . '/fdp', 'dcterms:title', 'Castor EDC VASCA FAIR Data Point');
        $graph->addLiteral('https://' . $this->url . '/fdp', 'dcterms:identifier', 'CastorEDC-VASCA');
        $graph->addLiteral('https://' . $this->url . '/fdp', 'dcterms:hasVersion', '0.1');
        $graph->addLiteral(
            'https://' . $this->url . '/fdp',
            'dcterms:description',
            'Lorum ipsum dolar si amet.'
        );
        $graph->addResource('https://' . $this->url . '/fdp', 'dcterms:publisher', 'https://vascern.eu/');
        $graph->addResource(
            'https://' . $this->url . '/fdp',
            'dcterms:language',
            'http://id.loc.gov/vocabulary/iso639-1/en'
        );
        $graph->addResource(
            'https://' . $this->url . '/fdp',
            'dcterms:license',
            'TBD'
        );
        $graph->addResource(
            'https://' . $this->url . '/fdp',
            'http://www.re3data.org/schema/3-0#dataCatalog',
            'https://' . $this->url . '/fdp/vasca'
        );

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            array('content-type' => 'text/turtle')
        );
    }

    /**
     * @Route("/demo", name="rdf_renderer")
     */
    public function demoAction()
    {

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
        }


        return $this->render('rdf-list.html.twig', $templateData,
            new Response(
                'Content',
                Response::HTTP_OK,
                array('content-type' => 'text/turtle')
            ));
    }
    
}
