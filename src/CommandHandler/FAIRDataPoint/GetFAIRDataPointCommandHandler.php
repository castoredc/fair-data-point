<?php
declare(strict_types=1);

namespace App\CommandHandler\FAIRDataPoint;

use App\Command\FAIRDataPoint\GetFAIRDataPointCommand;
use App\Entity\FAIRData\FAIRDataPoint;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetFAIRDataPointCommandHandler
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
