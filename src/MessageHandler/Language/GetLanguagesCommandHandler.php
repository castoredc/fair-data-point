<?php
declare(strict_types=1);

namespace App\MessageHandler\Language;

use App\Api\Resource\Language\LanguagesApiResource;
use App\Entity\FAIRData\Language;
use App\Message\Language\GetLanguagesCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetLanguagesCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetLanguagesCommand $command): LanguagesApiResource
    {
        $licenses = $this->em->getRepository(Language::class)->findAll();

        return new LanguagesApiResource($licenses);
    }
}
