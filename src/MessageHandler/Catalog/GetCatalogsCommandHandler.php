<?php
declare(strict_types=1);

namespace App\MessageHandler\Catalog;

use App\Entity\FAIRData\Catalog;
use App\Message\Catalog\GetCatalogsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetCatalogsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return Catalog[] */
    public function __invoke(GetCatalogsCommand $message): array
    {
        $catalogs = $this->em->getRepository(Catalog::class)->findAll();

        $return = [];

        foreach ($catalogs as $catalog) {
            if (! $this->security->isGranted('view', $catalog)) {
                continue;
            }

            $return[] = $catalog;
        }

        return $return;
    }
}
