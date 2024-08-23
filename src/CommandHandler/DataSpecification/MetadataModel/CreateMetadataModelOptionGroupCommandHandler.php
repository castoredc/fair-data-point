<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelOptionGroupCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroupOption;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMetadataModelOptionGroupCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(CreateMetadataModelOptionGroupCommand $command): void
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $optionGroup = new MetadataModelOptionGroup($metadataModelVersion, $command->getTitle(), $command->getDescription());
        $metadataModelVersion->addOptionGroup($optionGroup);

        foreach ($command->getOptions() as $index => $option) {
            $option['order'] = $index;
            $optionGroup->addOption(MetadataModelOptionGroupOption::fromData($option));
        }

        $this->em->persist($optionGroup);
        $this->em->persist($metadataModelVersion);

        $this->em->flush();
    }
}
