<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\CreateCatalogCommand;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Exception\NoAccessPermission;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;
use function assert;

#[AsMessageHandler]
class CreateCatalogCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateCatalogCommand $command): Catalog
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        /** @var FAIRDataPoint[] $fdp */
        $fdp = $this->em->getRepository(FAIRDataPoint::class)->findAll();

        $slug = $command->getSlug();

        $catalog = new Catalog($slug);
        $catalog->setFairDataPoint($fdp[0]);
        $catalog->setAcceptSubmissions($command->isAcceptSubmissions());
        $catalog->addPermissionForUser($user, PermissionType::manage());

        $submissionsAccessesData = $command->isAcceptSubmissions() ? $command->isSubmissionAccessesData() : false;
        $catalog->setSubmissionAccessesData($submissionsAccessesData);

        $this->em->persist($catalog);
        $this->em->flush();

        return $catalog;
    }
}
