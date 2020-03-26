<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\CatalogApiResource;
use App\Api\Resource\CatalogBrandApiResource;
use App\Entity\FAIRData\Catalog;
use App\Exception\CatalogNotFoundException;
use App\Message\Api\Study\GetCatalogBrandCommand;
use App\Message\Api\Study\GetCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCatalogBrandCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param GetCatalogBrandCommand $message
     *
     * @return CatalogBrandApiResource
     * @throws CatalogNotFoundException
     */
    public function __invoke(GetCatalogBrandCommand $message): CatalogBrandApiResource
    {
        /** @var Catalog|null $catalog */
        $catalog = $this->em->getRepository(Catalog::class)->findOneBy(['slug' => $message->getSlug()]);

        if($catalog === null)
        {
            throw new CatalogNotFoundException();
        }

        return new CatalogBrandApiResource($catalog);
    }
}
