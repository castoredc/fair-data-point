<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UIController extends AbstractController
{
    /**
     * @return RedirectResponse
     *
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('fdp_render');
    }

    /**
     * @Route("/login", name="login")
     * @Route("/my-studies", name="my_studies")
     */
    public function react(): Response
    {
        return $this->render(
            'react.html.twig'
        );
    }

    /**
     * @Route("/login/{catalog}", name="login_catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function catalogLogin(Catalog $catalog): Response
    {
        return $this->render(
            'react.html.twig'
        );
    }

    /**
     * @Route("/my-studies/{catalog}/study/add", name="add_study")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addStudy(Catalog $catalog): Response
    {
        return $this->render(
            'react.html.twig'
        );
    }

    /**
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/details", name="study_metadata_details")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/centers", name="study_metadata_centers")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/contacts", name="study_metadata_contact")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/consent", name="study_metadata_consent")
     * @Route("/my-studies/{catalog}/study/{studyId}/metadata/finished", name="study_metadata_finished")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function studyMetadata(Catalog $catalog, Study $study): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        return $this->render(
            'react.html.twig'
        );
    }

    /**
     * @Route("/redirect-login", name="redirect_login")
     */
    public function loginRedirect(Request $request): Response
    {
        return $this->redirectToRoute('fdp_render');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render(
            'react.html.twig'
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
            'react.html.twig'
        );
    }
}
