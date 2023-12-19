<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataDictionary;

use App\Command\Data\DataDictionary\ImportDataDictionaryCommand;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use App\Exception\InvalidDataDictionaryVersion;
use App\Exception\NoAccessPermission;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Factory\Data\DataDictionary\DataDictionaryGroupFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;
use function file_get_contents;
use function json_decode;

#[AsMessageHandler]
class ImportDataDictionaryCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;
    private DataDictionaryGroupFactory $dataDictionaryGroupFactory;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        DataDictionaryGroupFactory $dataDictionaryGroupFactory
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->dataDictionaryGroupFactory = $dataDictionaryGroupFactory;
    }

    public function __invoke(ImportDataDictionaryCommand $command): DataDictionaryVersion
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataDictionary = $command->getDataDictionary();
        $version = $command->getVersion();
        $file = $command->getFile();

        if (! $file->isValid()) {
            throw new InvalidFile($file->getErrorMessage());
        }

        $contents = file_get_contents($file->getPathname());

        if ($contents === false) {
            throw new EmptyFile();
        }

        $json = json_decode($contents, true);

        if ($json === null) {
            throw new InvalidJSON();
        }

        $groups = $json['groups'];

        if ($dataDictionary->hasVersion($version)) {
            throw new InvalidDataDictionaryVersion();
        }

        $newVersion = new DataDictionaryVersion($version);

        // Add groups
        $newGroups = new ArrayCollection();
        $newVariables = new ArrayCollection();

        // TODO: Add variables

        foreach ($groups as $group) {
            $newGroup = $this->dataDictionaryGroupFactory->createFromJson($newVersion, $newVariables, $group);
            $newGroups->add($newGroup);
        }

        $newVersion->setGroups($newGroups);

        $dataDictionary->addVersion($newVersion);

        $this->em->persist($newVersion);
        $this->em->persist($dataDictionary);

        $this->em->flush();

        return $newVersion;
    }
}
