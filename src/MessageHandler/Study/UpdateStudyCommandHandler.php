<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\NoAccessPermissionToStudy;
use App\Message\Study\UpdateStudyCommand;
use App\Security\CastorServer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class UpdateStudyCommandHandler implements MessageHandlerInterface
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

    public function __invoke(UpdateStudyCommand $command): Study
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermissionToStudy();
        }

        $study->setSlug($command->getSlug());
        $study->setName($command->getName());
        $study->setSourceId($command->getSourceId());
        $study->setIsPublished($command->isPublished());

        if ($study instanceof CastorStudy) {
            $server = $this->em->getRepository(CastorServer::class)->find($command->getSourceServer());
            assert($server instanceof CastorServer);

            $study->setServer($server);
        }

        $this->em->persist($study);
        $this->em->flush();

        return $study;
    }
}
