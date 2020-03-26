<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Language;
use App\Exception\CatalogNotFoundException;
use App\Exception\StudyAlreadyHasDatasetException;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\PublishStudyInCatalogCommand;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PublishStudyInCatalogCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(PublishStudyInCatalogCommand $message): void
    {
        /** @var Study|null $study */
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

        /** @var Catalog|null $catalog */
        $catalog = $this->em->getRepository(Catalog::class)->findOneBy(['slug' => $message->getCatalog()]);

        /** @var Language|null $catalog */
        $language = $this->em->getRepository(Language::class)->find('en');

        if($study === null)
        {
            throw new StudyNotFoundException();
        }

        if($catalog === null)
        {
            throw new CatalogNotFoundException();
        }

        if($study->getDataset())
        {
            throw new StudyAlreadyHasDatasetException();
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($study->getLatestMetadata()->getBriefName() . ' ' . uniqid());

        $dataset = new Dataset($slug, new ArrayCollection(), $language, null, null, null);
        $dataset->setStudy($study);

        $this->em->persist($dataset);
        $this->em->persist($study);

        $catalog->addDataset($dataset);
        $this->em->persist($catalog);

        $this->em->flush();
    }
}
