<?php
declare(strict_types=1);

namespace App\MessageHandler\License;

use App\Api\Resource\License\LicensesApiResource;
use App\Entity\FAIRData\License;
use App\Message\License\GetLicensesCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetLicensesCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetLicensesCommand $command): LicensesApiResource
    {
        $licenses = $this->em->getRepository(License::class)->findAll();

        return new LicensesApiResource($licenses);
    }
}
