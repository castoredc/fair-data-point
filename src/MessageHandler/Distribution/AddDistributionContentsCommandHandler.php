<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\RDF\RDFDistribution;
use App\Exception\InvalidDistributionType;
use App\Message\Distribution\AddDistributionContentsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddDistributionContentsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws InvalidDistributionType
     */
    public function __invoke(AddDistributionContentsCommand $message): void
    {
        if ($message->getType() === 'rdf') {
            $distribution = new RDFDistribution(
                $message->getDistribution(),
                $message->getAccessRights(),
                false
            );
        } elseif ($message->getType() === 'csv') {
            $distribution = new CSVDistribution(
                $message->getDistribution(),
                $message->getAccessRights(),
                false,
                $message->getIncludeAllData()
            );
        } else {
            throw new InvalidDistributionType();
        }

        $this->em->persist($distribution);

        $this->em->flush();
    }
}
