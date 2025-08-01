<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\ImportDataModelVersionCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Exception\DataSpecification\DataModel\InvalidDataModelVersion;
use App\Exception\NoAccessPermission;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Factory\DataSpecification\DataModel\DataModelModuleFactory;
use App\Factory\DataSpecification\DataModel\NamespacePrefixFactory;
use App\Factory\DataSpecification\DataModel\NodeFactory;
use App\Factory\DataSpecification\DataModel\PredicateFactory;
use App\Factory\DataSpecification\DataModel\TripleFactory;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function file_get_contents;
use function json_decode;

#[AsMessageHandler]
class ImportDataModelVersionCommandHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private NamespacePrefixFactory $namespacePrefixFactory,
        private NodeFactory $nodeFactory,
        private PredicateFactory $predicateFactory,
        private DataModelModuleFactory $dataModelModuleFactory,
        private TripleFactory $tripleFactory,
    ) {
    }

    public function __invoke(ImportDataModelVersionCommand $command): DataModelVersion
    {
        $dataModel = $command->getDataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $dataModel)) {
            throw new NoAccessPermission();
        }

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
