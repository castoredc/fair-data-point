<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataDictionary;

use App\Command\DataSpecification\DataDictionary\GetDataDictionariesCommand;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetDataDictionariesCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return DataDictionary[] */
    public function __invoke(GetDataDictionariesCommand $command): array
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        return $this->em->getRepository(DataDictionary::class)->findAll();
    }
}
