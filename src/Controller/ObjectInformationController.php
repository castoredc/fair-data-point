<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 21/05/2019
 * Time: 15:13
 */

namespace App\Controller;


use EasyRdf_Graph;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ObjectInformationController extends Controller
{
    /**
     * @Route("/object", name="object_information")
     * @param Request $request
     * @return Response
     */
    public function objectInformationAction(Request $request)
    {
        if(!$request->get('iri'))
        {
            return new JsonResponse([
                'success' => false,
                'error' => 'Please provide an IRI'
            ], 400);
        }

        $format = null;
        $data = null;

        // TODO escaping
        if(strpos($request->get('iri'), getenv("FDP_URL")) !== false)
        {
            // Local
            $format = 'turtle';
        }

        #$graph = new EasyRdf_Graph($request->get('iri'), $data, $format);

        try {
            $graph = EasyRdf_Graph::newAndLoad($request->get('iri'), $format);
        } catch (\EasyRdf_Http_Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        } catch (\EasyRdf_Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }

        if(!$graph->label())
        {
            return new JsonResponse([
                'success' => false,
                'label' => 'Could not fetch data from IRI'
            ], 404);
        }
        return new JsonResponse([
            'success' => true,
            'label' => ucfirst($graph->label()->getValue())
        ]);
    }
}
