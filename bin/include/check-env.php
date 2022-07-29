<?php
declare(strict_types=1);

function checkDockerCompose(): bool
{
    if (! shell_exec('which docker-compose')) {
        printLine(error('Docker Compose is not installed (docker-compose binary not found).'));
        printLine(warning('Please follow the EDC setup guide in the wiki.'));

        return false;
    }

    $requiredVersion = '1.20.0';
    preg_match('(\d+\.\d+\.\d+)', trim(shell_exec('docker-compose --version')), $matches);
    if (version_compare($matches[0], $requiredVersion, '<')) {
        printLine(error('docker-compose binary is not up to date, >= ' . $requiredVersion . ' needed.'));
        printLine(warning('Please update it following the install instructions in the EDC setup guide.'));

        return false;
    }

    return true;
}

function checkDocker(): bool
{
    if (! shell_exec('which docker')) {
        printLine(error('Docker is not installed (docker binary not found).'));
        printLine(warning('Please follow the EDC setup guide in the wiki.'));

        return false;
    }

    $requiredVersion = '18.04.0';
    preg_match('(\d+\.\d+\.\d+)', trim(shell_exec('docker --version')), $matches);
    if (version_compare($matches[0], $requiredVersion, '<')) {
        printLine(error('docker binary is not up to date, >= ' . $requiredVersion . ' needed.'));
        printLine(warning('Please update it following the install instructions in the EDC setup guide.'));

        return false;
    }

    return true;
}

function checkAuthJsonFile(): bool
{
    if (! file_exists(PROJECT_ROOT . 'auth.json')) {
        printLine(error('auth.json file not found!'));
        printLine(warning('copy auth.json.dist to auth.json and fill in your satis.castoredc.net username and password.'));

        return false;
    }

    return true;
}
