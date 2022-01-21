<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\CreateDatasetForStudyCommand;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Permission\DatasetPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Security\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function uniqid;

class CreateDatasetForStudyCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateDatasetForStudyCommand $command): Dataset
    {
        $study = $command->getStudy();
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermissionToStudy();
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($study->getName() . ' ' . uniqid());

        $dataset = new Dataset($slug);
        $dataset->setStudy($study);

        $dataset->addPermissionForUser($user, PermissionType::manage());

        $study->addDataset($dataset);

        $this->em->persist($dataset);
        $this->em->persist($study);

        $this->em->flush();

        return $dataset;
    }
}
