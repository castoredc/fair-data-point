<?php
/** @phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint */
declare(strict_types=1);

namespace App\Console;

use App\Command\FAIRDataPoint\GetFAIRDataPointCommand;
use App\Entity\FAIRData\FAIRDataPoint;
use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use function assert;

#[AsCommand(name: 'app:ping-home')]
class PingHomeCommand extends Command
{
    private Client $client;

    public function __construct(private MessageBusInterface $bus)
    {
        $this->client = new Client();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Sends request to home.fairdatapoint.org to keep metadata up to date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            [
                'Ping Home Command',
                '============',
                '',
            ]
        );

        $envelope = $this->bus->dispatch(new GetFAIRDataPointCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $fdp = $handledStamp->getResult();
        assert($fdp instanceof FAIRDataPoint);

        $this->client->post('https://home.fairdatapoint.org/', [
            'json' => [
                'clientUrl' => $fdp->getIri()->getValue(),
            ],
        ]);

        return Command::SUCCESS;
    }
}
