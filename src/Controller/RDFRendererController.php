<?php

namespace App\Controller;

use App\Model\ApiClient;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RDFRendererController extends Controller
{

    private $studyId = '57051B03-59C1-23A3-3ADA-7AA791481606';
    private $secret = '25e3956844bb52ea99c4230c139a3f67';
    private $clientId = '34413D8C-05D9-A974-F3ED-654A2EBE2FDC';


    /**
     * @Route("/fdp", name="fdp_render")
     */
    public function fdpAction()
    {
        

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
