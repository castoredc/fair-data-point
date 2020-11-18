<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\ImportDataModelCommand;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Exception\InvalidDataModelVersion;
use App\Exception\NoAccessPermission;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Factory\Data\DataModel\DataModelModuleFactory;
use App\Factory\Data\DataModel\NamespacePrefixFactory;
use App\Factory\Data\DataModel\NodeFactory;
use App\Factory\Data\DataModel\PredicateFactory;
use App\Factory\Data\DataModel\TripleFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function file_get_contents;
use function json_decode;

class ImportDataModelCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private Security $security;
    private NamespacePrefixFactory $namespacePrefixFactory;
    private NodeFactory $nodeFactory;
    private PredicateFactory $predicateFactory;
    private DataModelModuleFactory $dataModelModuleFactory;
    private TripleFactory $tripleFactory;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        NamespacePrefixFactory $namespacePrefixFactory,
        NodeFactory $nodeFactory,
        PredicateFactory $predicateFactory,
        DataModelModuleFactory $dataModelModuleFactory,
        TripleFactory $tripleFactory
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->namespacePrefixFactory = $namespacePrefixFactory;
        $this->nodeFactory = $nodeFactory;
        $this->predicateFactory = $predicateFactory;
        $this->dataModelModuleFactory = $dataModelModuleFactory;
        $this->tripleFactory = $tripleFactory;
    }

    public function __invoke(ImportDataModelCommand $command): DataModelVersion
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();
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

        $nodes = $json['nodes'];
        $modules = $json['modules'];
        $prefixes = $json['prefixes'];
        $predicates = $json['predicates'];

        if ($dataModel->hasVersion($version)) {
            throw new InvalidDataModelVersion();
        }

        $newVersion = new DataModelVersion($version);

        // Add prefixes
        foreach ($prefixes as $prefix) {
            $newVersion->addPrefix($this->namespacePrefixFactory->createFromJson($prefix));
        }

        // Add nodes
        $newNodes = new ArrayCollection();

        foreach ($nodes as $nodeType) {
            foreach ($nodeType as $node) {
                $newNode = $this->nodeFactory->createFromJson($newVersion, $node);
                $newNodes->set($node['id'], $newNode);
                $newVersion->addNode($newNode);
            }
        }

        // Add predicates
        $newPredicates = new ArrayCollection();

        foreach ($predicates as $predicate) {
            $newPredicate = $this->predicateFactory->createFromJson($newVersion, $predicate);
            $newPredicates->set($predicate['id'], $newPredicate);
            $newVersion->addPredicate($newPredicate);
        }

        // Add modules
        $newModules = new ArrayCollection();

        foreach ($modules as $module) {
            $newModule = $this->dataModelModuleFactory->createFromJson($newVersion, $newNodes, $module);

            foreach ($module['triples'] as $triple) {
                $newTriple = $this->tripleFactory->createFromJson($newModule, $newNodes, $newPredicates, $triple);
                $newModule->addTriple($newTriple);
            }

            $newModules->add($newModule);
        }

        $newVersion->setGroups($newModules);

        $dataModel->addVersion($newVersion);

        $this->em->persist($newVersion);
        $this->em->persist($dataModel);

        $this->em->flush();

        return $newVersion;
    }
}
