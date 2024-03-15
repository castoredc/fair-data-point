<?php
declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\FAIRData\Dataset;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DatasetController extends AbstractController
{
    /**
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}", name="dashboard_study_dataset")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/metadata", name="dashboard_study_dataset_metadata")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/permissions", name="dashboard_study_dataset_permissions")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions", name="dashboard_study_dataset_distributions")
     * @Route("/dashboard/studies/{studyId}/datasets/{dataset}/distributions/add", name="dashboard_study_dataset_distributions_add")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}", name="dashboard_catalog_dataset")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/metadata", name="dashboard_catalog_dataset_metadata")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/permissions", name="dashboard_catalog_dataset_permissions")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions", name="dashboard_catalog_dataset_distributions")
     * @Route("/dashboard/catalogs/{catalog}/datasets/{dataset}/distributions/add", name="dashboard_catalog_dataset_distributions_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function dataset(Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        return $this->render(
            'react.html.twig',
            ['title' => 'Dataset']
        );
    }
}
