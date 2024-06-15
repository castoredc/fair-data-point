<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DataModelController extends AbstractController
{
    /** @Route("/dashboard/data-models", name="dashboard_data_models") */
    public function dataModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /** @Route("/dashboard/data-models/add", name="dashboard_data_model_add") */
    public function addDataModel(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /**
     * @Route("/dashboard/data-models/{model}", name="dashboard_model")
     * @Route("/dashboard/data-models/{model}/versions", name="dashboard_model_versions")
     * @Route("/dashboard/data-models/{model}/permissions", name="dashboard_model_permissions")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function adminModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => $dataModel->getTitle()]
        );
    }

    /**
     * @Route("/dashboard/data-models/{model}/{version}/modules", name="dashboard_model_modules")
     * @Route("/dashboard/data-models/{model}/{version}/modules/{moduleId}", name="dashboard_model_module")
     * @Route("/dashboard/data-models/{model}/{version}/prefixes", name="dashboard_model_prefixes")
     * @Route("/dashboard/data-models/{model}/{version}/preview", name="dashboard_model_preview")
     * @Route("/dashboard/data-models/{model}/{version}/import-export", name="dashboard_model_importexport")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "dataModel", "version": "version"}})
     */
    public function adminModelVersion(DataModel $dataModel, DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/dashboard/data-models/{model}/{version}/nodes/{nodeType}", name="dashboard_model_nodes")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "dataModel", "version": "version"}})
     */
    public function adminModelVersionNodes(DataModel $dataModel, DataModelVersion $dataModelVersion, string $nodeType): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
