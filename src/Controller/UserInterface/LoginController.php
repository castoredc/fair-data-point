<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function react(): Response
    {
        return $this->render(
            'react.html.twig',
            ['title' => 'FAIR Data Point | Log in']
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
    }

    /**
     * @Route("/login/{catalog}", name="login_catalog")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function catalogLogin(Catalog $catalog): Response
    {
        if (! $catalog->isAcceptingSubmissions()) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'react.html.twig',
            ['title' => $catalog->getLatestMetadata()->getTitle()->getTextByLanguageString('en')->getText() . ' | Log in']
        );
    }

    /**
     * @Route("/redirect-login", name="redirect_login")
     */
    public function loginRedirect(Request $request): Response
    {
        return $this->redirectToRoute('fdp');
    }
}
