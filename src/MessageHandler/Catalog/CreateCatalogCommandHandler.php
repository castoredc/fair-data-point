<?php
declare(strict_types=1);

namespace App\MessageHandler\Catalog;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Exception\NoAccessPermission;
use App\Message\Catalog\CreateCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateCatalogCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateCatalogCommand $command): Catalog
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        /** @var FAIRDataPoint[] $fdp */
        $fdp = $this->em->getRepository(FAIRDataPoint::class)->findAll();

        $catalog = new Catalog($command->getSlug());
        $catalog->setFairDataPoint($fdp[0]);
        $catalog->setAcceptSubmissions($command->isAcceptSubmissions());

        $submissionsAccessesData = $command->isAcceptSubmissions() ? $command->isSubmissionAccessesData() : false;
        $catalog->setSubmissionAccessesData($submissionsAccessesData);

        $this->em->persist($catalog);
        $this->em->flush();

        return $catalog;
    }
}
