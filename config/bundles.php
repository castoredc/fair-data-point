<?php
declare(strict_types=1);

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle;
use KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle;
use Sentry\SentryBundle\SentryBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

return [
    FrameworkBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    WebpackEncoreBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    KnpUOAuth2ClientBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    FreshDoctrineEnumBundle::class => ['all' => true],
    StofDoctrineExtensionsBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    SentryBundle::class => ['all' => true],
    ZenstruckFoundryBundle::class => ['dev' => true, 'test' => true],
    DAMADoctrineTestBundle::class => ['test' => true],
];
