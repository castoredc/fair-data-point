<?php
/** @phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint */
declare(strict_types=1);

namespace App\Console;

use App\Command\DataSpecification\MetadataModel\ImportMetadataModelVersionCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Version;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use function assert;

#[AsCommand(name: 'app:metadata:create')]
class CreateMetadataModelCommand extends Command
{
    /** @phpcs:ignore */
    public const METADATA_MODEL_ID = '78db7aa4-9349-4fa0-b10f-73f180a4d94e';

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Insert new Castor metadata schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'Create metadata schema',
                '============',
                '',
            ]
        );

        $metadataModel = new MetadataModel('Castor metadata model', '');
        $metadataModel->setId(self::METADATA_MODEL_ID);

        // Allow for force setting ID
        $metadata = $this->em->getClassMetadata($metadataModel::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        $envelope = $this->bus->dispatch(
            new ImportMetadataModelVersionCommand(
                $metadataModel,
                new UploadedFile(__DIR__ . '/metadata-schema.json', 'metadata-schema.json'),
                new Version('1.0.0')
            )
        );

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return Command::SUCCESS;
    }
}
