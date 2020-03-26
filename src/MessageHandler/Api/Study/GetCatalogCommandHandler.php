<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\CatalogApiResource;
use App\Entity\FAIRData\Catalog;
use App\Exception\CatalogNotFoundException;
use App\Message\Api\Study\GetCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCatalogCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param GetCatalogCommand $message
     *
     * @return CatalogApiResource
     * @throws CatalogNotFoundException
     */
    public function __invoke(GetCatalogCommand $message): CatalogApiResource
    {
        /** @var Catalog|null $catalog */
        $catalog = $this->em->getRepository(Catalog::class)->findOneBy(['slug' => $message->getSlug()]);

        if($catalog === null)
        {
            throw new CatalogNotFoundException();
        }

        return new CatalogApiResource($catalog);
    }
}
