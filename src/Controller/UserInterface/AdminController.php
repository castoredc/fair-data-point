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
     * @Route("/catalogs", name="admin_catalogs")
     * @Route("/models", name="admin_models")
     * @Route("/studies", name="admin_studies")
     * @Route("/datasets", name="admin_datasets")
     * @Route("/dictionaries", name="admin_dictionaries")
     */
    public function adminModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/model/{model}", name="admin_model")
     * @Route("/model/{model}/versions", name="admin_model_versions")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function adminModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
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

    /**
     * @Route("/model/{model}/{version}/modules", name="admin_model_modules")
     * @Route("/model/{model}/{version}/prefixes", name="admin_model_prefixes")
     * @Route("/model/{model}/{version}/nodes", name="admin_model_nodes")
     * @Route("/model/{model}/{version}/preview", name="admin_model_preview")
     * @Route("/model/{model}/{version}/import-export", name="admin_model_importexport")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "dataModel", "version": "version"}})
     */
    public function adminModelVersion(DataModel $dataModel, DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
