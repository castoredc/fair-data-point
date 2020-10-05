<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\ClearDistributionContentCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class ClearDistributionContentCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(ClearDistributionContentCommand $message): void
    {
        $distribution = $message->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $contents = $distribution->getContents();

        if ($contents instanceof CSVDistribution) {
            $contents->setElements(new ArrayCollection());
        }

        $this->em->persist($contents);
        $this->em->flush();
    }
}
