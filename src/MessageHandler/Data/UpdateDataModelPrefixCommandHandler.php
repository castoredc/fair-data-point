<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Iri;
use App\Message\Data\UpdateDataModelPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDataModelPrefixCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateDataModelPrefixCommand $command): void
    {
        $prefix = $command->getDataModelPrefix();

        $prefix->setPrefix($command->getPrefix());
        $prefix->setUri(new Iri($command->getUri()));

        $this->em->persist($prefix);

        $this->em->flush();
    }
}
