<?php
declare(strict_types=1);

use App\Deployer\Deployer;
use EasyCorp\Bundle\EasyDeployBundle\Configuration\DefaultConfiguration;

return new class extends Deployer
{
    protected string $environment = 'production';
    protected string $server = 'fdp.castoredc.com';

    public function configure(): DefaultConfiguration
    {
        parent::setup();

        return $this->getConfigBuilder()
            ->server('fdp.castoredc.com')
            ->deployDir('/var/www/fdp.castoredc.com')
            ->repositoryUrl(self::REPOSITORY_URL)
            ->repositoryBranch($this->branch)
            ->fixPermissionsWithChgrp('www-data')
            ->composerInstallFlags('--prefer-dist --no-interaction --no-dev --no-scripts --quiet --ignore-platform-reqs');
    }

    public function beforePublishing(): void
    {
        parent::beforePublishing();

        $tag = sprintf('production_%s', $this->version);

        $this->gitRepo->createTag($tag);
        $this->gitRepo->push(null, ['origin', $tag]);

        $this->gitRepo->createTag('production', '--force');
        $this->gitRepo->push(null, ['origin', 'production', '--force']);
    }
};
