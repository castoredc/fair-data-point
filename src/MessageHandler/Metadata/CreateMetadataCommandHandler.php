<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Version;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

abstract class CreateMetadataCommandHandler implements MessageHandlerInterface
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function updateVersionNumber(?Version $currentVersion, VersionType $versionUpdate): Version
    {
        if ($currentVersion === null) {
            return new Version(self::DEFAULT_VERSION_NUMBER);
        }

        $version = new Version();

        if ($versionUpdate->isMajor()) {
            $version->setMajor($currentVersion->getMajor() + 1);
            $version->setMinor(0);
            $version->setPatch(0);
        } elseif ($versionUpdate->isMinor()) {
            $version->setMajor($currentVersion->getMajor());
            $version->setMinor($currentVersion->getMinor() + 1);
            $version->setPatch(0);
        } elseif ($versionUpdate->isPatch()) {
            $version->setMajor($currentVersion->getMajor());
            $version->setMinor($currentVersion->getMinor());
            $version->setPatch($currentVersion->getPatch() + 1);
        }

        return $version;
    }

    protected function parseLocalizedText(?LocalizedText $localizedText): ?LocalizedText
    {
        if ($localizedText === null) {
            return null;
        }

        foreach ($localizedText->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $text->setLanguage($this->getLanguage($text->getLanguageCode()));
        }

        return $localizedText;
    }

    protected function getLanguage(string $languageCode): Language
    {
        $repository = $this->em->getRepository(Language::class);

        /** @var Language $language */
        $language = $repository->find($languageCode);

        return $language;
    }

    protected function getLicense(string $licenseId): License
    {
        $repository = $this->em->getRepository(License::class);

        /** @var License $license */
        $license = $repository->find($licenseId);

        return $license;
    }
}
