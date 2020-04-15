<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\Distribution;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin | Catalogs']
        );
    }

    /**
     * @Route("/admin/{catalog}", name="admin_catalog")
     * @Route("/admin/{catalog}/study/add", name="admin_study_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function adminCatalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin | ' . $catalog->getTitle()->getTextByLanguageString('en')->getText()]
        );
    }

    /**
     * @Route("/admin/{catalog}/study/{studyId}", name="admin_study")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/add/details", name="admin_study_metadata_details_add")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/add/centers", name="admin_study_metadata_centers_add")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/add/contacts", name="admin_study_metadata_contacts_add")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/add/consent", name="admin_study_metadata_consent_add")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/update/details", name="admin_study_metadata_details_update")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/update/centers", name="admin_study_metadata_centers_update")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/update/contacts", name="admin_study_metadata_contacts_update")
     * @Route("/admin/{catalog}/study/{studyId}/metadata/update/consent", name="admin_study_metadata_consent_update")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function adminStudy(Catalog $catalog, Study $study): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }

    /**
     * @Route("/admin/{catalog}/dataset/{dataset}/distribution", name="admin_study_distributions")
     * @Route("/admin/{catalog}/dataset/{dataset}/distribution/add", name="admin_add_study_distribution")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function adminDataset(Catalog $catalog, Dataset $dataset): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig',
            ['title' => 'Admin']
        );
    }
}
