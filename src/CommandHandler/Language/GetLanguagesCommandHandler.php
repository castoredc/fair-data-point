<?php
declare(strict_types=1);

namespace App\CommandHandler\Language;

use App\Api\Resource\Language\LanguagesApiResource;
use App\Command\Language\GetLanguagesCommand;
use App\Entity\FAIRData\Language;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetLanguagesCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(GetLanguagesCommand $command): LanguagesApiResource
    {
        $licenses = $this->em->getRepository(Language::class)->findAll();

        return new LanguagesApiResource($licenses);
    }
}
