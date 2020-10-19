<?php
declare(strict_types=1);

namespace App\Deployer;

use Cz\Git\GitRepository;
use EasyCorp\Bundle\EasyDeployBundle\Context;
use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;
use EasyCorp\Bundle\EasyDeployBundle\Server\Property;
use EasyCorp\Bundle\EasyDeployBundle\Server\Server;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use function assert;
use function end;
use function explode;
use function sprintf;

abstract class Deployer extends DefaultDeployer
{
    public const REPOSITORY_URL = 'git@github.com:castoredc/fair-data-point.git';

    protected GitRepository $gitRepo;
    protected QuestionHelper $questionHelper;

    protected string $environment;
    protected string $server;
    protected string $branch;
    protected string $version;

    public function initialize(Context $context): void
    {
        parent::initialize($context);

        $question = new ConfirmationQuestion('Are you sure you want to deploy? [y/N] ', false);

        $deploying = (bool) $this->ask($question);

        if (! $deploying) {
            $this->log('<h1><fg=red>Not deploying</></h1>');
            exit;
        }
    }

    public function setup(): void
    {
        $this->questionHelper = new QuestionHelper();
        $this->gitRepo = new GitRepository($this->getContext()->getLocalProjectRootDir());

        $this->branch = $this->gitRepo->getCurrentBranchName();

        $this->log(sprintf('<h1>You are about to deploy <bg=yellow;options=bold> %s </> to <bg=yellow;options=bold> %s </> (%s)</h1>', $this->branch, $this->server, $this->environment));
    }

    /** @return mixed */
    protected function ask(Question $question)
    {
        return $this->questionHelper->ask($this->getContext()->getInput(), $this->getContext()->getOutput(), $question);
    }

    public function beforeStartingDeploy(): void
    {
        $this->gitRepo->fetch();

        if (! $this->gitRepo->hasChanges()) {
            return;
        }

        $this->log('<h1><fg=red>You have un-pushed local changes</></h1>');

        $question = new ConfirmationQuestion('Do you want to deploy, without pushing these changes? [y/N] ', false);

        $deploying = (bool) $this->ask($question);

        if (! $deploying) {
            $this->log('<h1><fg=red>Not deploying</></h1>');
            exit;
        }
    }

    public function beforePreparing(): void
    {
        $this->log('<h2>Copy ENV files</h2>');
        $this->runRemote(sprintf('cp -RPp {{ deploy_dir }}/repo/.env {{ project_dir }}'));
        $this->runRemote(sprintf('cp -RPp {{ deploy_dir }}/.env.local {{ project_dir }}'));
    }

    public function beforePublishing(): void
    {
        $server = $this->getServers()->findAll()[0];
        assert($server instanceof Server);
        $this->version = $this->getCurrentVersion($server);

        $this->buildUi($server);
        $this->handleMigrations();

        $this->pushVersion();
    }

    protected function getCurrentVersion(Server $server): string
    {
        $projectDir = $server->get(Property::project_dir);
        $pathParts = explode('/', $projectDir);

        return (string) end($pathParts);
    }

    public function pushVersion(): void
    {
        $this->runRemote(
            sprintf('cd {{ project_dir }} && echo "APP_VERSION=%s" > ./.env.prod.local', $this->version)
        );
    }

    protected function buildUi(Server $server): void
    {
        $projectDir = $server->get(Property::project_dir);

        $this->log('<h1>Building UI</h1>');
        $this->runLocal('yarn install');
        $this->runLocal('yarn run encore production');

        $this->log('<h1>Deploying UI</h1>');
        $this->runLocal(sprintf(
            'rsync --progress -crDpLt --force --delete ./public/build/ %s:%s/public/build/',
            $server->getHost(),
            $projectDir
        ));
    }

    protected function handleMigrations(): void
    {
        $this->log('<h1>Running migrations</h1>');
        $this->runRemote('bin/console d:m:m --no-interaction --quiet');
    }
}
