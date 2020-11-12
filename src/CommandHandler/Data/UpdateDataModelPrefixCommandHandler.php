<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Command\Data\UpdateDataModelPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateDataModelPrefixCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateDataModelPrefixCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $prefix = $command->getDataModelPrefix();

        $prefix->setPrefix($command->getPrefix());
        $prefix->setUri(new Iri($command->getUri()));

        $this->em->persist($prefix);

        $this->em->flush();
    }
}
