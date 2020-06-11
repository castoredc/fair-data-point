<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyStudiesController extends AbstractController
{
    /**
     * @Route("/my-studies/{catalog}/study/add", name="add_study")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addStudy(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);

        return $this->render(
            'react.html.twig',
            ['title' => $catalog->getLatestMetadata()->getTitle()->getTextByLanguageString('en')->getText() . ' | Add study']
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
            'react.html.twig',
            ['title' => $catalog->getLatestMetadata()->getTitle()->getTextByLanguageString('en')->getText() . ' | Add metadata']
        );
    }
}
