<?php

namespace App\MessageHandler\Distribution;

use App\Entity\FAIRData\Distribution\CSVDistribution\CSVDistribution;
use App\Message\Distribution\ClearDistributionContentCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearDistributionContentCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(ClearDistributionContentCommand $message): void
    {
        $distribution = $message->getDistribution();

        if($distribution instanceof CSVDistribution)
        {
            $distribution->setElements(new ArrayCollection());
        }

        $this->em->persist($distribution);
        $this->em->flush();
    }
}