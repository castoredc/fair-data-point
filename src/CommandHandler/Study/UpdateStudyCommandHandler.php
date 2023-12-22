<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\UpdateStudyCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\NoAccessPermissionToStudy;
use App\Security\CastorServer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function uniqid;

#[AsMessageHandler]
class UpdateStudyCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

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

        $slug = $command->getSlug();

        // Check for duplicate slugs
        if ($this->em->getRepository(Study::class)->findBySlug($slug) !== null) {
            $slug .= '-' . uniqid();
        }

        $study->setSlug($slug);
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
