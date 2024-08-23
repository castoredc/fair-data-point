<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\CreateDatasetForStudyCommand;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Exception\NoAccessPermissionToStudy;
use App\Security\Authorization\Voter\StudyVoter;
use App\Security\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function uniqid;

#[AsMessageHandler]
class CreateDatasetForStudyCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(CreateDatasetForStudyCommand $command): Dataset
    {
        $study = $command->getStudy();
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted(StudyVoter::EDIT, $study)) {
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
