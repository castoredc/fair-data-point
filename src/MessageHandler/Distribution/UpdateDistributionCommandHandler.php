<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\FAIRData\License;
use App\Exception\LanguageNotFound;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\UpdateDistributionCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateDistributionCommandHandler implements MessageHandlerInterface
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

    /**
     * @throws LanguageNotFound
     */
    public function __invoke(UpdateDistributionCommand $message): void
    {
        $distribution = $message->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        /** @var License|null $license */
        $license = $this->em->getRepository(License::class)->find($message->getLicense());

        $distribution->setSlug($message->getSlug());
        $distribution->setLicense($license);

        $contents = $distribution->getContents();
        $contents->setAccessRights($message->getAccessRights());

        if ($contents instanceof CSVDistribution) {
            $contents->setIncludeAll($message->getIncludeAllData());
        }

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->flush();
    }
}
