<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Language;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\AddManualCastorStudyCommand;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function uniqid;

class AddManualCastorStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(AddManualCastorStudyCommand $message): Study
    {
        $study = new Study($message->getStudyId(), $message->getStudyName(), $message->getStudySlug(), null);
        $study->setEnteredManually(true);

        /** @var Language|null $language */
        $language = $this->em->getRepository(Language::class)->find('en');

        $slugify = new Slugify();
        $slug = $slugify->slugify($message->getStudySlug() . ' ' . uniqid());

        $dataset = new Dataset($slug, new ArrayCollection(), $language, null, null, null);
        $dataset->setStudy($study);
        $study->setDataset($dataset);

        try {
            $this->em->persist($study);
            $this->em->persist($dataset);

            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new StudyAlreadyExists();
        }

        return $study;
    }
}
