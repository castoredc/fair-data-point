<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\DeleteTripleCommand;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteTripleCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(DeleteTripleCommand $command): void
    {
        $triple = $command->getTriple();
        $metadataModel = $triple->getMetadataModelVersion()->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $this->em->remove($triple);

        $this->em->flush();
    }
}
