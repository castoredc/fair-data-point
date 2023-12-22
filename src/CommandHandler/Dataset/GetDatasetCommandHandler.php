<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\GetDatasetCommand;
use App\Entity\FAIRData\Dataset;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetDatasetCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetDatasetCommand $command): Dataset
    {
        $dataset = $this->em->getRepository(Dataset::class)->find($command->getId());

        if ($dataset === null) {
            throw new NotFound();
        }

        if (! $this->security->isGranted('view', $dataset)) {
            throw new NoAccessPermission();
        }

        assert($dataset instanceof Dataset);

        return $dataset;
    }
}
