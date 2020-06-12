<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\FAIRData\License;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\UpdateDistributionCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDistributionCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws LanguageNotFound
     */
    public function __invoke(UpdateDistributionCommand $message): void
    {
        /** @var License|null $license */
        $license = $this->em->getRepository(License::class)->find($message->getLicense());

        $distribution = $message->getDistribution();
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
