<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\RDF\RDFDistributionPrefix;
use App\Entity\Iri;
use App\Exception\InvalidDistributionType;
use App\Message\Distribution\AddRDFDistributionPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddRDFDistributionPrefixCommandHandler implements MessageHandlerInterface
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
    public function __invoke(AddRDFDistributionPrefixCommand $message): void
    {
        $distribution = $message->getDistribution();

        $prefix = new RDFDistributionPrefix($message->getPrefix(), new Iri($message->getUri()));
        $distribution->addPrefix($prefix);

        $this->em->persist($prefix);
        $this->em->persist($distribution);

        $this->em->flush();
    }
}
