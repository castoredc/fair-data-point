<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\UpdateCatalogCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\CatalogVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class UpdateCatalogCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UpdateCatalogCommand $command): void
    {
        $catalog = $command->getCatalog();

        if (! $this->security->isGranted(CatalogVoter::EDIT, $catalog)) {
            throw new NoAccessPermission();
        }

        $defaultMetadataModel = $this->em->getRepository(MetadataModel::class)->find($command->getDefaultMetadataModelId());
        assert($defaultMetadataModel instanceof MetadataModel);

        $slug = $command->getSlug();

        $catalog->setSlug($slug);
        $catalog->setAcceptSubmissions($command->isAcceptSubmissions());
        $catalog->setDefaultMetadataModel($defaultMetadataModel);

        $submissionsAccessesData = $command->isAcceptSubmissions() ? $command->isSubmissionAccessesData() : false;
        $catalog->setSubmissionAccessesData($submissionsAccessesData);

        $this->em->persist($catalog);
        $this->em->flush();
    }
}
