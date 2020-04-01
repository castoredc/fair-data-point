<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Exception\CatalogNotExceptingSubmissions;
use App\Message\Study\AddStudyToCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddStudyToCatalogCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(AddStudyToCatalogCommand $message): void
    {
        if (! $message->getCatalog()->isAcceptingSubmissions()) {
            throw new CatalogNotExceptingSubmissions();
        }

        $message->getCatalog()->addDataset($message->getStudy()->getDataset());
        $this->em->persist($message->getCatalog());

        $this->em->flush();
    }
}
