<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\ImportMetadataModelVersionCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Exception\DataSpecification\MetadataModel\InvalidMetadataModelVersion;
use App\Exception\NoAccessPermission;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Factory\DataSpecification\MetadataModel\FormFactory;
use App\Factory\DataSpecification\MetadataModel\MetadataModelModuleFactory;
use App\Factory\DataSpecification\MetadataModel\NamespacePrefixFactory;
use App\Factory\DataSpecification\MetadataModel\NodeFactory;
use App\Factory\DataSpecification\MetadataModel\OptionGroupFactory;
use App\Factory\DataSpecification\MetadataModel\PredicateFactory;
use App\Factory\DataSpecification\MetadataModel\TripleFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function file_get_contents;
use function json_decode;

#[AsMessageHandler]
class ImportMetadataModelVersionCommandHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private NamespacePrefixFactory $namespacePrefixFactory,
        private NodeFactory $nodeFactory,
        private PredicateFactory $predicateFactory,
        private OptionGroupFactory $optionGroupFactory,
        private FormFactory $formFactory,
        private MetadataModelModuleFactory $metadataModelModuleFactory,
        private TripleFactory $tripleFactory,
    ) {
    }

    public function __invoke(ImportMetadataModelVersionCommand $command): MetadataModelVersion
    {
        $metadataModel = $command->getMetadataModel();

        if (! $this->security->isGranted('edit', $metadataModel)) {
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
        $optionGroups = $json['optionGroups'];
        $forms = $json['forms'];

        if ($metadataModel->hasVersion($version)) {
            throw new InvalidMetadataModelVersion();
        }

        $newVersion = new MetadataModelVersion($version);

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

        // Add option groups
        $newOptionGroups = new ArrayCollection();

        foreach ($optionGroups as $optionGroup) {
            $newOptionGroup = $this->optionGroupFactory->createFromJson($newVersion, $optionGroup);
            $newOptionGroups->set($optionGroup['id'], $newOptionGroup);
            $newVersion->addOptionGroup($newOptionGroup);
        }

        // Add option groups
        $newForms = new ArrayCollection();

        foreach ($forms as $form) {
            $newForm = $this->formFactory->createFromJson($newVersion, $form, $newNodes, $newOptionGroups);
            $newForms->set($form['id'], $newForm);
            $newVersion->addForm($newForm);
        }

        // Add modules
        $newModules = new ArrayCollection();

        foreach ($modules as $module) {
            $newModule = $this->metadataModelModuleFactory->createFromJson($newVersion, $newNodes, $module);

            foreach ($module['triples'] as $triple) {
                $newTriple = $this->tripleFactory->createFromJson($newModule, $newNodes, $newPredicates, $triple);
                $newModule->addTriple($newTriple);
            }

            $newModules->add($newModule);
        }

        $newVersion->setGroups($newModules);

        $metadataModel->addVersion($newVersion);

        $this->em->persist($newVersion);
        $this->em->persist($metadataModel);

        $this->em->flush();

        return $newVersion;
    }
}
