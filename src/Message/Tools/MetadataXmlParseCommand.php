<?php
declare(strict_types=1);

namespace App\Message\Tools;

class MetadataXmlParseCommand
{
    private string $xmlBody;

    public function __construct(string $xmlBody)
    {
        $this->xmlBody = $xmlBody;
    }

    public function getXmlBody(): string
    {
        return $this->xmlBody;
    }
}
