<?php
declare(strict_types=1);

namespace App\CommandHandler\License;

use App\Api\Resource\License\LicensesApiResource;
use App\Command\License\GetLicensesCommand;
use App\Entity\FAIRData\License;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetLicensesCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(GetLicensesCommand $command): LicensesApiResource
    {
        $licenses = $this->em->getRepository(License::class)->findAll();

        return new LicensesApiResource($licenses);
    }
}
