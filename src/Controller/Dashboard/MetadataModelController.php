<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MetadataModelController extends AbstractController
{
    #[Route(path: '/dashboard/metadata-models', name: 'dashboard_metadata_models')]
    public function metadataModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    #[Route(path: '/dashboard/metadata-models/add', name: 'dashboard_metadata_model_add')]
    public function addMetadataModel(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    #[Route(path: '/dashboard/metadata-models/{model}', name: 'dashboard_metadata_model')]
    #[Route(path: '/dashboard/metadata-models/{model}/versions', name: 'dashboard_metadata_model_versions')]
    #[Route(path: '/dashboard/metadata-models/{model}/permissions', name: 'dashboard_metadata_model_permissions')]
    public function adminModel(
        #[MapEntity(mapping: ['model' => 'id'])]
        MetadataModel $metadataModel,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModel);

        return $this->render(
            'react.html.twig',
            ['title' => $metadataModel->getTitle()]
        );
    }

    #[Route(path: '/dashboard/metadata-models/{model}/{version}/display', name: 'dashboard_metadata_model_display')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/display/{resourceType}', name: 'dashboard_metadata_model_display_resource')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/modules', name: 'dashboard_metadata_model_modules')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/modules/{moduleId}', name: 'dashboard_metadata_model_module')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/forms', name: 'dashboard_metadata_model_forms')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/forms/{form_id}', name: 'dashboard_metadata_model_form')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/option-group', name: 'dashboard_metadata_model_option_groups')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/prefixes', name: 'dashboard_metadata_model_prefixes')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/preview', name: 'dashboard_metadata_model_preview')]
    #[Route(path: '/dashboard/metadata-models/{model}/{version}/import-export', name: 'dashboard_metadata_model_importexport')]
    public function adminModelVersion(
        #[MapEntity(mapping: ['model' => 'id'])]
        MetadataModel $metadataModel,
        #[MapEntity(mapping: ['model' => 'metadataModel', 'version' => 'version'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    #[Route(path: '/dashboard/metadata-models/{model}/{version}/nodes/{nodeType}', name: 'dashboard_metadata_model_nodes')]
    public function adminModelVersionNodes(
        #[MapEntity(mapping: ['model' => 'id'])]
        MetadataModel $metadataModel,
        #[MapEntity(mapping: ['model' => 'metadataModel', 'version' => 'version'])]
        MetadataModelVersion $metadataModelVersion,
        string $nodeType,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
