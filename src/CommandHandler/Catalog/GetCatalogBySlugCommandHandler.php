<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\GetCatalogBySlugCommand;
use App\Entity\FAIRData\Catalog;
use App\Exception\CatalogNotFound;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;

#[AsMessageHandler]
class GetCatalogBySlugCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetCatalogBySlugCommand $command): Catalog
    {
        $catalog = $this->em->getRepository(Catalog::class)->findBySlug($command->getSlug());

        if ($catalog === null) {
            throw new CatalogNotFound();
        }

        if (! $this->security->isGranted('view', $catalog)) {
            throw new NoAccessPermission();
        }

        return $catalog;
    }
}
