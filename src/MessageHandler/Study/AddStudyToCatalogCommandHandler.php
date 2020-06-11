<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Exception\CatalogNotExceptingSubmissions;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddStudyToCatalogCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(AddStudyToCatalogCommand $command): void
    {
        if (! $command->getCatalog()->isAcceptingSubmissions() && ! $this->security->isGranted('ROLE_ADMIN')) {
            throw new CatalogNotExceptingSubmissions();
        }

        $command->getCatalog()->addStudy($command->getStudy());
        $this->em->persist($command->getCatalog());

        $this->em->flush();
    }
}
