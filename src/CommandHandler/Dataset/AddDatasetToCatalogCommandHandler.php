<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\AddDatasetToCatalogCommand;
use App\Exception\CatalogNotExceptingSubmissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddDatasetToCatalogCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
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
