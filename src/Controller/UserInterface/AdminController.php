<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Data\DataDictionary\DataDictionary;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="admin")
     */
    public function admin(): Response
    {
        return $this->redirectToRoute('admin_catalogs');
    }

    /**
     * @Route("/dictionary/{dataDicationary}", name="admin_dictionary")
     * @Route("/dictionary/{dataDicationary}/versions", name="admin_dictionary_versions")
     * @ParamConverter("dataDicationary", options={"mapping": {"dataDicationary": "id"}})
     */
    public function adminDataDictionary(DataDictionary $dataDicationary): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
