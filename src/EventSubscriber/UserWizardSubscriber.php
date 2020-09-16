<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Api\Controller\Security\UserApiController;
use App\Controller\Wizard\UserOnboardingWizardController;
use App\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use function assert;
use function count;
use function is_array;
use function urlencode;

class UserWizardSubscriber implements EventSubscriberInterface
{
    /** @var Security */
    private $security;
    /** @var RouterInterface */
    private $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if ($this->security->getUser() === null) {
            return;
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof UserOnboardingWizardController || $controller instanceof UserApiController) {
            return;
        }

        if (! $this->shouldShowWizard($user)) {
            return;
        }

        $event->setController(
            function () use ($event): RedirectResponse {
                $url = $this->router->generate('wizard_user_onboarding') . '?origin=' . urlencode($event->getRequest()->getRequestUri());

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
