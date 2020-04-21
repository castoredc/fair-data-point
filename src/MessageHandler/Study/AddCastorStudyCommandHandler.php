<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Security\CastorServer;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Language;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\AddCastorStudyCommand;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function in_array;
use function uniqid;

class AddCastorStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(EntityManagerInterface $em, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    public function __invoke(AddCastorStudyCommand $message): Study
    {
        if (! in_array($message->getStudyId(), $message->getUser()->getStudies(), true)) {
            throw new NoAccessPermissionToStudy();
        }

        $this->apiClient->setUser($message->getUser());
        $study = $this->apiClient->getStudy($message->getStudyId());

        /** @var CastorServerRepository $serverRepository */
        $serverRepository = $this->em->getRepository(CastorServer::class);
        $server = $serverRepository->findServerByUrl($message->getUser()->getServer());
        $study->setServer($server);

        /** @var Language|null $language */
        $language = $this->em->getRepository(Language::class)->find('en');

        $slugify = new Slugify();
        $slug = $slugify->slugify($study->getSlug() . ' ' . uniqid());

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
