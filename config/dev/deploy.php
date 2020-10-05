<?php
declare(strict_types=1);

use App\Deployer\Deployer;
use EasyCorp\Bundle\EasyDeployBundle\Configuration\DefaultConfiguration;

return new class extends Deployer
{
    protected string $environment = 'dev';
    protected string $server = 'fdp.castoredc.dev';

    public function configure(): DefaultConfiguration
    {
        parent::setup();

        return $this->getConfigBuilder()
            ->server($this->server)
            ->deployDir('/srv/www/fdp.castoredc.dev')
            ->repositoryUrl(self::REPOSITORY_URL)
            ->repositoryBranch($this->branch)
            ->fixPermissionsWithAcl('web')
            ->composerInstallFlags('--prefer-dist --no-interaction --no-dev --no-scripts --quiet');
    }

    public function beforeFinishingDeploy(): void
    {
        $this->log('<h1>Restarting servers</h1>');
        $this->runRemote('sudo /etc/init.d/nginx restart');
        $this->runRemote('sudo /etc/init.d/php7.4-fpm restart');
    }
};
