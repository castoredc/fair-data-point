<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Entity\Data\CSV\CSVDistributionElement;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
use App\Exception\NoAccessPermission;
use App\Command\Distribution\AddCSVDistributionContentCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddCSVDistributionContentCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(AddCSVDistributionContentCommand $command): void
    {
        $distribution = $command->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        if ($command->getType() === CSVDistributionElement::FIELD_ID) {
            $distribution->addElement(new CSVDistributionElementFieldId($command->getValue()));
        } elseif ($command->getType() === CSVDistributionElement::VARIABLE_NAME) {
            $distribution->addElement(new CSVDistributionElementVariableName($command->getValue()));
        }

        $this->em->persist($distribution);
        $this->em->flush();
    }
}
