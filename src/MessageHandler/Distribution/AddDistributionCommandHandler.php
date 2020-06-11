<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Exception\InvalidDistributionType;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\AddDistributionCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddDistributionCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws LanguageNotFound
     * @throws InvalidDistributionType
     */
    public function __invoke(AddDistributionCommand $message): Distribution
    {
        /** @var License|null $license */
        $license = $this->em->getRepository(License::class)->find($message->getLicense());

        if ($message->getType() !== 'rdf' && $message->getType() !== 'csv') {
            throw new InvalidDistributionType();
        }

        $distribution = new Distribution(
            $message->getSlug(),
            $message->getDataset()
        );

        if($license) {
            $distribution->setLicense($license);
        }

        $this->em->persist($distribution);
        $this->em->flush();

        return $distribution;
    }
}
