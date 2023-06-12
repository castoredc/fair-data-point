<?php
/** @phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint */
declare(strict_types=1);

namespace App\Console;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Service\EncryptionService;
use App\Service\TripleStoreBasedDistributionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function bin2hex;
use function random_bytes;
use function sprintf;
use function str_replace;

class MigrateToTripleStoresCommand extends Command
{
    /** @phpcs:ignore */
    protected static $defaultName = 'app:migrate-triplestores';

    private EntityManagerInterface $em;
    private TripleStoreBasedDistributionService $tripleStoreBasedDistributionService;
    private EncryptionService $encryptionService;

    public function __construct(
        EntityManagerInterface $em,
        TripleStoreBasedDistributionService $tripleStoreBasedDistributionService,
        EncryptionService $encryptionService
    ) {
        $this->em = $em;
        $this->tripleStoreBasedDistributionService = $tripleStoreBasedDistributionService;
        $this->encryptionService = $encryptionService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migrates databases to new triple store in Stardog');
    }

    /** @inheritDoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'Triple Store migration',
                '============',
                '',
            ]
        );

        $databases = $this->em->getRepository(DistributionDatabaseInformation::class)->findAll();

        foreach ($databases as $databaseInformation) {
            $output->writeln(sprintf('Migrating <%s>', $databaseInformation->getDatabase()));

            $username = $databaseInformation->getDecryptedUsername($this->encryptionService)->exposeAsString();

            $readOnlyUserName = str_replace(DistributionDatabaseInformation::USERNAME_PREPEND, DistributionDatabaseInformation::READ_ONLY_USERNAME_PREPEND, $username);
            $readOnlyPassword = bin2hex(random_bytes(32));

            $databaseInformation->setReadOnlyUsername($this->encryptionService, $readOnlyUserName);
            $databaseInformation->setReadOnlyPassword($this->encryptionService, $readOnlyPassword);

            $output->writeln(sprintf('Role <%s>', $databaseInformation->getRole()));
            $output->writeln(sprintf('User <%s>', $username));
            $output->writeln(sprintf('RO User <%s>', $readOnlyUserName));

            $this->tripleStoreBasedDistributionService->createDatabase($databaseInformation);
            $this->tripleStoreBasedDistributionService->createUsers($databaseInformation, $this->encryptionService);

            $output->writeln(sprintf('Migrated <%s>', $databaseInformation->getDatabase()));
            $output->writeln('');
        }

        return Command::SUCCESS;
    }
}
