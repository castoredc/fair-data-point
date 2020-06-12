<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Entity\PaginatedResultCollection;
use App\Message\Dataset\CreateDatasetForStudyCommand;
use App\Message\Dataset\GetDatasetsByStudyCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Repository\DatasetRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDatasetForStudyCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateDatasetForStudyCommand $command): Dataset
    {
        $study = $command->getStudy();
        $slugify = new Slugify();
        $slug = $slugify->slugify($study->getName() . ' ' . uniqid());

        $dataset = new Dataset($slug);
        $dataset->setStudy($study);

        $study->addDataset($dataset);

        $this->em->persist($dataset);
        $this->em->persist($study);

        $this->em->flush();

        return $dataset;
    }
}
