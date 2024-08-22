<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\UpdateDataModelPrefixCommand;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDataModelPrefixCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UpdateDataModelPrefixCommand $command): void
    {
        $prefix = $command->getDataModelPrefix();
        $dataModel = $prefix->getDataModelVersion()->getDataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $dataModel)) {
            throw new NoAccessPermission();
        }

        $prefix->setPrefix($command->getPrefix());
        $prefix->setUri(new Iri($command->getUri()));

        $this->em->persist($prefix);

        $this->em->flush();
    }
}
