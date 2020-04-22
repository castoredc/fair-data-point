<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistributionElement;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
use App\Message\Distribution\AddCSVDistributionContentCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddCSVDistributionContentCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(AddCSVDistributionContentCommand $message): void
    {
        $distribution = $message->getDistribution();

        if ($message->getType() === CSVDistributionElement::FIELD_ID) {
            $distribution->addElement(new CSVDistributionElementFieldId($message->getValue()));
        } elseif ($message->getType() === CSVDistributionElement::VARIABLE_NAME) {
            $distribution->addElement(new CSVDistributionElementVariableName($message->getValue()));
        }

        $this->em->persist($distribution);
        $this->em->flush();
    }
}
