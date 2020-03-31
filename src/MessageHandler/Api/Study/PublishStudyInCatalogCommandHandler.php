<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Language;
use App\Exception\CatalogNotExceptingSubmissions;
use App\Exception\StudyAlreadyHasDataset;
use App\Exception\StudyAlreadyHasSameDataset;
use App\Message\Api\Study\PublishStudyInCatalogCommand;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function uniqid;

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
        /** @var Language|null $language */
        $language = $this->em->getRepository(Language::class)->find('en');

        if ($message->getStudy()->getDataset() !== null) {
            if ($message->getStudy()->getDataset()->hasCatalog($message->getCatalog())) {
                throw new StudyAlreadyHasSameDataset();
            }

            throw new StudyAlreadyHasDataset();
        }

        if (! $message->getCatalog()->isAcceptSubmissions()) {
            throw new CatalogNotExceptingSubmissions();
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($message->getStudy()->getLatestMetadata()->getBriefName() . ' ' . uniqid());

        $dataset = new Dataset($slug, new ArrayCollection(), $language, null, null, null);
        $dataset->setStudy($message->getStudy());

        $this->em->persist($dataset);
        $this->em->persist($message->getStudy());

        $message->getCatalog()->addDataset($dataset);
        $this->em->persist($message->getCatalog());

        $this->em->flush();
    }
}
