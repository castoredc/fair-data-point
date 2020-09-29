<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Exception\CatalogNotExceptingSubmissions;
use App\Message\Dataset\AddDatasetToCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddDatasetToCatalogCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(AddDatasetToCatalogCommand $command): void
    {
        $catalog = $command->getCatalog();

        if (! $catalog->isAcceptingSubmissions() && ! $this->security->isGranted('ROLE_ADMIN')) {
            throw new CatalogNotExceptingSubmissions();
        }

        $dataset = $command->getDataset();
        $study = $dataset->getStudy();

        $catalog->addDataset($dataset);
        $catalog->addStudy($study);

        $this->em->persist($dataset);
        $this->em->persist($study);
        $this->em->persist($catalog);

        $this->em->flush();
    }
}
