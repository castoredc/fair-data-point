<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Message\Distribution\CreateDistributionCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDistributionCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateDistributionCommand $message): Distribution
    {
        $distribution = new Distribution(
            $message->getSlug(),
            $message->getDataset()
        );

        /** @var License|null $license */
        $license = $this->em->getRepository(License::class)->find($message->getLicense());
        $distribution->setLicense($license);


        if ($message->getType()->isRdf()) {
            /** @var DataModel|null $dataModel */
            $dataModel = $this->em->getRepository(DataModel::class)->find($message->getDataModel());

            $contents = new RDFDistribution(
                $distribution,
                $message->getAccessRights(),
                false
            );

            $contents->setDataModel($dataModel);
        }

        if ($message->getType()->isCsv()) {
            $contents = new CSVDistribution(
                $distribution,
                $message->getAccessRights(),
                false,
                $message->getIncludeAllData()
            );
        }

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->flush();

        return $distribution;
    }
}
