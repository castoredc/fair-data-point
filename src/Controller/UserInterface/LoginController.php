<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use App\Entity\FAIRData\Catalog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function react(): Response
    {
        return $this->render(
            'react.html.twig',
            ['title' => 'FAIR Data Point | Log in']
        );
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
    }

    #[Route(path: '/login/{catalog}', name: 'login_catalog')]
    public function catalogLogin(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['catalog' => 'slug'])]
    Catalog $catalog): Response
    {
        if (! $catalog->isAcceptingSubmissions()) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'react.html.twig',
            ['title' => 'Log in']
        );
    }

    #[Route(path: '/redirect-login', name: 'redirect_login')]
    public function loginRedirect(): Response
    {
        return $this->redirectToRoute('fdp');
    }
}
