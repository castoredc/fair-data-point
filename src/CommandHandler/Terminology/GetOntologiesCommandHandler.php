<?php
declare(strict_types=1);

namespace App\CommandHandler\Terminology;

use App\Command\Terminology\GetOntologiesCommand;
use App\Entity\Terminology\Ontology;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetOntologiesCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Ontology[]
     */
    public function __invoke(GetOntologiesCommand $command): array
    {
        return $this->em->getRepository(Ontology::class)->findAll();
    }
}
