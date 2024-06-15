<?php
declare(strict_types=1);

namespace App\Command\Tools;

class MetadataXmlParseCommand
{
    public function __construct(private string $xmlBody)
    {
    }

    public function getXmlBody(): string
    {
        return $this->xmlBody;
    }
}
