<?php
declare(strict_types=1);

namespace App\MessageHandler\FAIRDataPoint;

use App\Entity\FAIRData\FAIRDataPoint;
use App\Message\FAIRDataPoint\GetFAIRDataPointCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetFAIRDataPointCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetFAIRDataPointCommand $command): FAIRDataPoint
    {
        /** @var FAIRDataPoint[] $fdp */
        $fdp = $this->em->getRepository(FAIRDataPoint::class)->findAll();

        return $fdp[0];
    }
}
