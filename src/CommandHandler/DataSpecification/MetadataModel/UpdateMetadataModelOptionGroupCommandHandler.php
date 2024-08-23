<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelOptionGroupCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroupOption;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMetadataModelOptionGroupCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UpdateMetadataModelOptionGroupCommand $command): void
    {
        $optionGroup = $command->getOptionGroup();
        $metadataModel = $optionGroup->getMetadataModelVersion()->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $optionGroup->setTitle($command->getTitle());
        $optionGroup->setDescription($command->getDescription());

        $existingOptions = $optionGroup->getOptionsWithId();

        foreach ($command->getOptions() as $index => $option) {
            $option['order'] = $index;
            if (isset($option['id']) && isset($existingOptions[$option['id']])) {
                $existingOption = $existingOptions[$option['id']];
                $existingOption->setTitle($option['title']);
                $existingOption->setDescription($option['description']);
                $existingOption->setOrder($option['order']);
                $existingOption->setValue($option['value']);

                unset($existingOptions[$option['id']]);
            } else {
                $optionGroup->addOption(MetadataModelOptionGroupOption::fromData($option));
            }
        }

        foreach ($existingOptions as $existingOption) {
            $optionGroup->removeOption($existingOption);
            $this->em->remove($existingOption);
        }

        $this->em->persist($optionGroup);

        $this->em->flush();
    }
}
