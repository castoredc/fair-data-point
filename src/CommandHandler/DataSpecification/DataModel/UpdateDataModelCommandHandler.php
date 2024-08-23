<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\UpdateDataModelCommand;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDataModelCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UpdateDataModelCommand $command): void
    {
        $dataModel = $command->getDataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $dataModel)) {
            throw new NoAccessPermission();
        }

        $dataModel->setTitle($command->getTitle());
        $dataModel->setDescription($command->getDescription());

        $this->em->persist($dataModel);
        $this->em->flush();
    }
}
