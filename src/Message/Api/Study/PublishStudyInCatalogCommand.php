<?php

namespace App\Message\Api\Study;

class PublishStudyInCatalogCommand
{
    /** @var string */
    private $studyId;

    /** @var string */
    private $catalog;

    /**
     * PublishDataSetInCatalogCommand constructor.
     *
     * @param string $studyId
     * @param string $catalog
     */
    public function __construct(string $studyId, string $catalog)
    {
        $this->studyId = $studyId;
        $this->catalog = $catalog;
    }

    /**
     * @return string
     */
    public function getStudyId(): string
    {
        return $this->studyId;
    }

    /**
     * @return string
     */
    public function getCatalog(): string
    {
        return $this->catalog;
    }
}