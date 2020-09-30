<?php
declare(strict_types=1);

use Cz\Git\GitRepository;
use EasyCorp\Bundle\EasyDeployBundle\Configuration\DefaultConfiguration;
use EasyCorp\Bundle\EasyDeployBundle\Context;
use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;
use EasyCorp\Bundle\EasyDeployBundle\Server\Property;
use EasyCorp\Bundle\EasyDeployBundle\Server\Server;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

return new class extends DefaultDeployer
{
    private GitRepository $gitRepo;

    private QuestionHelper $questionHelper;

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

    public function configure(): DefaultConfiguration
    {
        $this->questionHelper = new QuestionHelper();
        $this->gitRepo = new GitRepository($this->getContext()->getLocalProjectRootDir());

        $localBranch = $this->gitRepo->getCurrentBranchName();
        $question = new Question(sprintf('Which branch do you want to deploy? [%s]', $localBranch), $localBranch);

        $branch = $this->ask($question);

        return $this->getConfigBuilder()
            ->server('fdp.castoredc.dev')
            ->deployDir('/srv/www/fdp.castoredc.dev')
            ->repositoryUrl(sprintf('git@github.com:%s.git', 'castoredc/fair-data-point'))
            ->repositoryBranch($branch)
            ->fixPermissionsWithAcl('web')
            ->composerInstallFlags('--prefer-dist --no-interaction --no-dev --no-scripts --quiet');
    }

    public function beforeStartingDeploy(): void
    {
        $branchToDeploy = $this->getConfig('repositoryBranch');

        $this->gitRepo->fetch();
        $localBranch = $this->gitRepo->getCurrentBranchName();

        if ($localBranch !== $branchToDeploy) {
            throw new Exception(sprintf(
                "You are trying to deploy branch '%s' your branch is currently set to '%s'.\n" .
                "We need to build webpack locally, therefore we require that you are on the same branch\n" .
                'And that its up to date with the remote.',
                $branchToDeploy,
                $localBranch
            ));
        }

//        if ($this->gitRepo->hasChanges()) {
//            throw new Exception(sprintf(
//                "You have un-pushed local changes.\n" .
//                'We need to build webpack locally, therefore we require that you are up to date with the remote.',
//                $branchToDeploy,
//                $localBranch
//            ));
//        }
//
//        $this->log('<h1>Pulling in changes from remote git</h1>');
//        $this->gitRepo->pull();
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
        $projectDir = $server->get(Property::project_dir);

        $this->log('<h1>Building UI</h1>');
        $this->runLocal('yarn install');
        $this->runLocal('yarn run encore production');
        $this->runLocal(sprintf(
            'rsync --progress -crDpLt --force --delete ./public/build/ %s:%s/public/build/',
            $server->getHost(),
            $projectDir
        ));

        $this->handleMigrations();

        $version = $this->getCurrentVersionFromProjectDir($projectDir);

//        $tag = sprintf('production_%s', $version);
//
//        $this->gitRepo->createTag($tag);
//        $this->gitRepo->push(null, ['origin', $tag]);
//
//        $this->gitRepo->createTag('production', '--force');
//        $this->gitRepo->push(null, ['origin', 'production', '--force']);

        $this->pushVersion($version);
    }

    public function beforeFinishingDeploy(): void
    {
        $this->log('<h1>Restarting servers</h1>');
        $this->runRemote('sudo /etc/init.d/nginx restart');
        $this->runRemote('sudo /etc/init.d/php7.4-fpm restart');
    }

    public function pushVersion(string $version): void
    {
        $this->runRemote(
            sprintf('cd {{ project_dir }} && echo "APP_VERSION=%s" > ./.env.prod.local', $version)
        );
    }

    private function getCurrentVersionFromProjectDir(string $projectDir): string
    {
        $pathParts = explode('/', $projectDir);

        return (string) end($pathParts);
    }

    private function handleMigrations(): void
    {
        $this->log('<h1>Running migrations</h1>');
        $this->runRemote('bin/console d:m:m --no-interaction --quiet');
    }

    /** @return mixed */
    private function ask(Question $question)
    {
        return $this->questionHelper->ask($this->getContext()->getInput(), $this->getContext()->getOutput(), $question);
    }
};
