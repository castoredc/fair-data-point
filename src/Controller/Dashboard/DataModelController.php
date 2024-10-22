<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DataModelController extends AbstractController
{
    #[Route(path: '/dashboard/data-models', name: 'dashboard_data_models')]
    public function dataModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    #[Route(path: '/dashboard/data-models/add', name: 'dashboard_data_model_add')]
    public function addDataModel(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    #[Route(path: '/dashboard/data-models/{model}', name: 'dashboard_model')]
    #[Route(path: '/dashboard/data-models/{model}/versions', name: 'dashboard_model_versions')]
    #[Route(path: '/dashboard/data-models/{model}/permissions', name: 'dashboard_model_permissions')]
    public function adminModel(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => $dataModel->getTitle()]
        );
    }

    #[Route(path: '/dashboard/data-models/{model}/{version}/modules', name: 'dashboard_model_modules')]
    #[Route(path: '/dashboard/data-models/{model}/{version}/modules/{moduleId}', name: 'dashboard_model_module')]
    #[Route(path: '/dashboard/data-models/{model}/{version}/prefixes', name: 'dashboard_model_prefixes')]
    #[Route(path: '/dashboard/data-models/{model}/{version}/preview', name: 'dashboard_model_preview')]
    #[Route(path: '/dashboard/data-models/{model}/{version}/import-export', name: 'dashboard_model_importexport')]
    public function adminModelVersion(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'version'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    #[Route(path: '/dashboard/data-models/{model}/{version}/nodes/{nodeType}', name: 'dashboard_model_nodes')]
    public function adminModelVersionNodes(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'version'])]
        DataModelVersion $dataModelVersion,
        string $nodeType,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
