<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\UpdateDistributionCommand;
use Doctrine\Common\Collections\ArrayCollection;
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
        /** @var Language|null $language */
        $language = $this->em->getRepository(Language::class)->find($message->getLanguage());

        /** @var License|null $license */
        $license = $this->em->getRepository(License::class)->find($message->getLicense());

        if ($language === null) {
            throw new LanguageNotFound();
        }

        $distribution = $message->getDistribution();
        $distribution->setSlug($message->getSlug());
        $distribution->setTitle(new LocalizedText(new ArrayCollection([new LocalizedTextItem($message->getTitle(), $language)])));
        $distribution->setVersion($message->getVersion());
        $distribution->setDescription(new LocalizedText(new ArrayCollection([new LocalizedTextItem($message->getDescription(), $language)])));
        $distribution->setLanguage($language);
        $distribution->setLicense($license);
        $distribution->setAccessRights($message->getAccessRights());

        if ($distribution instanceof CSVDistribution) {
            $distribution->setIncludeAll($message->getIncludeAllData());
        }

        $this->em->persist($distribution);
        $this->em->flush();
    }
}
