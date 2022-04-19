<?php
/** @phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps */
declare(strict_types=1);

namespace App\CommandHandler\Tools;

use App\Command\Tools\MetadataXmlParseCommand;
use App\Exception\NoFieldsFound;
use App\Exception\NoMetadataTypesFound;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function simplexml_load_string;

class MetadataParseCommandHandler implements MessageHandlerInterface
{
    /**
     * @return array<mixed>
     *
     * @throws NoMetadataTypesFound
     */
    public function __invoke(MetadataXmlParseCommand $command): array
    {
        $xml = simplexml_load_string($command->getXmlBody());
        $return = [];

        if (! isset($xml->metadataTypes)) {
            throw new NoMetadataTypesFound();
        }

        $types = [];
        foreach ($xml->metadataTypes->children() as $type) {
            $types[(string) $type->type_id] = (string) $type->name;
        }

        $fieldContainers = $xml->xpath('//fields');

        /** @phpstan-ignore-next-line */
        if ($fieldContainers === false) {
            throw new NoFieldsFound();
        }

        foreach ($fieldContainers as $fieldContainer) {
            foreach ($fieldContainer->children() as $field) {
                $variableName = (string) $field->field_variable_name;

                if (! isset($field->metadata) || $variableName === '') {
                    continue;
                }

                foreach ($field->metadata->children() as $metadata) {
                    $type = (string) $metadata->metadata_type;
                    $typeName = $types[$type] ?? $type;
                    $id = (string) $metadata->metadata_id;
                    $description = (string) $metadata->metadata_description;
                    $value = (string) $metadata->metadata_value;

                    $return[] = [
                        'id' => $id,
                        'variableName' => $variableName,
                        'type' => $typeName,
                        'value' => $value,
                        'description' => $description,
                    ];
                }
            }
        }

        return $return;
    }
}
