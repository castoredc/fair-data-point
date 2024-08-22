<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MetadataModelController extends AbstractController
{
    /** @Route("/dashboard/metadata-models", name="dashboard_metadata_models") */
    public function metadataModels(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /** @Route("/dashboard/metadata-models/add", name="dashboard_metadata_model_add") */
    public function addMetadataModel(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Data models']
        );
    }

    /**
     * @Route("/dashboard/metadata-models/{model}", name="dashboard_metadata_model")
     * @Route("/dashboard/metadata-models/{model}/versions", name="dashboard_metadata_model_versions")
     * @Route("/dashboard/metadata-models/{model}/permissions", name="dashboard_metadata_model_permissions")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     */
    public function adminModel(MetadataModel $metadataModel): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModel);

        return $this->render(
            'react.html.twig',
            ['title' => $metadataModel->getTitle()]
        );
    }

    /**
     * @Route("/dashboard/metadata-models/{model}/{version}/display", name="dashboard_metadata_model_display")
     * @Route("/dashboard/metadata-models/{model}/{version}/display/{resourceType}", name="dashboard_metadata_model_display_resource")
     * @Route("/dashboard/metadata-models/{model}/{version}/modules", name="dashboard_metadata_model_modules")
     * @Route("/dashboard/metadata-models/{model}/{version}/modules/{moduleId}", name="dashboard_metadata_model_module")
     * @Route("/dashboard/metadata-models/{model}/{version}/forms", name="dashboard_metadata_model_forms")
     * @Route("/dashboard/metadata-models/{model}/{version}/forms/{form_id}", name="dashboard_metadata_model_form")
     * @Route("/dashboard/metadata-models/{model}/{version}/option-group", name="dashboard_metadata_model_option_groups")
     * @Route("/dashboard/metadata-models/{model}/{version}/prefixes", name="dashboard_metadata_model_prefixes")
     * @Route("/dashboard/metadata-models/{model}/{version}/preview", name="dashboard_metadata_model_preview")
     * @Route("/dashboard/metadata-models/{model}/{version}/import-export", name="dashboard_metadata_model_importexport")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadataModel", "version": "version"}})
     */
    public function adminModelVersion(MetadataModel $metadataModel, MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }

    /**
     * @Route("/dashboard/metadata-models/{model}/{version}/nodes/{nodeType}", name="dashboard_metadata_model_nodes")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadataModel", "version": "version"}})
     */
    public function adminModelVersionNodes(MetadataModel $metadataModel, MetadataModelVersion $metadataModelVersion, string $nodeType): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModel);

        return $this->render(
            'react.html.twig',
            ['title' => 'FDP Admin']
        );
    }
}
