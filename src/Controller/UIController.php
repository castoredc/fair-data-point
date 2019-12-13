<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 27/08/2019
 * Time: 14:51
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UIController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        return $this->redirectToRoute('fdp_render');
    }
}
