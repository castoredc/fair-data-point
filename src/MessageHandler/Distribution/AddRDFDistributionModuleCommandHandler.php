<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\RDF\RDFDistributionModule;
use App\Exception\InvalidDistributionType;
use App\Message\Distribution\AddRDFDistributionModuleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddRDFDistributionModuleCommandHandler implements MessageHandlerInterface
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
    public function __invoke(AddRDFDistributionModuleCommand $message): void
    {
        $distribution = $message->getDistribution();

        $module = new RDFDistributionModule($message->getTitle(), $message->getOrder(), $distribution);
        $distribution->addModule($module);

        $this->em->persist($module);
        $this->em->persist($distribution);

        $this->em->flush();
    }
}
