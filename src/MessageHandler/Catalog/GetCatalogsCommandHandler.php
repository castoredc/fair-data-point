<?php
declare(strict_types=1);

namespace App\MessageHandler\Catalog;

use App\Api\Resource\Catalog\CatalogsApiResource;
use App\Entity\FAIRData\Catalog;
use App\Message\Catalog\GetCatalogsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCatalogsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetCatalogsCommand $message): CatalogsApiResource
    {
        $catalogs = $this->em->getRepository(Catalog::class)->findAll();

        return new CatalogsApiResource($catalogs);
    }
}
