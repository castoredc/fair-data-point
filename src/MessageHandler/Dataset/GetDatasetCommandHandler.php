<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Message\Dataset\GetDatasetCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetDatasetCommandHandler implements MessageHandlerInterface
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

    public function __invoke(GetDatasetCommand $command): Dataset
    {
        $dataset = $this->em->getRepository(Dataset::class)->find($command->getId());

        if($dataset === null) {
            throw new NotFound();
        }

        if(! $this->security->isGranted('view', $dataset)) {
            throw new NoAccessPermission();
        }

        return $dataset;
    }
}
