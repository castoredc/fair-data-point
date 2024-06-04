<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static createdAt()
 * @method static static updatedAt()
 * @method static static resourceUrl()
 * @method static static metadataVersion()
 * @method static static distributionAccessUrl()
 * @method static static distributionMediaType()
 * @method bool isCreatedAt()
 * @method bool isUpdatedAt()
 * @method bool isResourceUrl()
 * @method bool isMetadataVersion()
 * @method bool isDistributionAccessUrl()
 * @method bool isDistributionMediaType()
 * @inheritDoc
 */
class MetadataPlaceholderLiterals extends Enum
{
    public const CREATED_AT = '##created_at##';
    public const UPDATED_AT = '##updated_at##';
    public const RESOURCE_URL = '##resource_url##';
    public const METADATA_VERSION = '##metadata_version##';
    public const DISTRIBUTION_ACCESS_URL = '##distribution_access_url##';
    public const DISTRIBUTION_MEDIA_TYPE = '##distribution_media_type##';
}
