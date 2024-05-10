<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Api\Controller\Agent\OrganizationApiController;
use App\Api\Controller\FAIRData\CountriesApiController;
use App\Api\Controller\Security\UserAffiliationApiController;
use App\Api\Controller\Security\UserApiController;
use App\Controller\Wizard\UserOnboardingWizardController;
use App\Entity\Enum\Wizard;
use App\Security\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use function assert;
use function count;
use function is_array;
use function urlencode;

class UserWizardSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private RouterInterface $router)
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if ($this->security->getUser() === null) {
            return;
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (
            $controller instanceof UserOnboardingWizardController ||
            $controller instanceof UserApiController ||
            $controller instanceof ErrorController ||
            $controller instanceof CountriesApiController ||
            $controller instanceof OrganizationApiController ||
            $controller instanceof UserAffiliationApiController
        ) {
            return;
        }

        if (! $this->shouldShowWizard($user)) {
            return;
        }

        $firstWizard = $user->getWizards()->first();
        assert($firstWizard instanceof Wizard);

        $event->setController(
            function () use ($event, $firstWizard): RedirectResponse {
                $url = $this->router->generate($firstWizard->getRoute()) . '?origin=' . urlencode($event->getRequest()->getRequestUri());

                return new RedirectResponse($url);
            }
        );
    }

    private function shouldShowWizard(User $user): bool
    {
        return count($user->getWizards()) > 0;
    }

    /** @inheritDoc */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }
}
