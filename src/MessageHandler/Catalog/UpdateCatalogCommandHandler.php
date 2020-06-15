<?php
declare(strict_types=1);

namespace App\MessageHandler\Catalog;

use App\Message\Catalog\UpdateCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateCatalogCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateCatalogCommand $command): void
    {
        $catalog = $command->getCatalog();
        $catalog->setSlug($command->getSlug());
        $catalog->setAcceptSubmissions($command->isAcceptSubmissions());

        $submissionsAccessesData = $command->isAcceptSubmissions() ? $command->isSubmissionAccessesData() : false;
        $catalog->setSubmissionAccessesData($submissionsAccessesData);

        $this->em->persist($catalog);
        $this->em->flush();
    }
}
