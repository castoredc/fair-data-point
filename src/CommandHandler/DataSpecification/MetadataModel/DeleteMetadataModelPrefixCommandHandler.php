<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelPrefixCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteMetadataModelPrefixCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(DeleteMetadataModelPrefixCommand $command): void
    {
        $prefix = $command->getMetadataModelPrefix();
        $metadataModel = $prefix->getMetadataModelVersion()->getMetadataModel();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $this->em->remove($prefix);

        $this->em->flush();
    }
}
