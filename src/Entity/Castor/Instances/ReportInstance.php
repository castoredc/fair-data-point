<?php


namespace App\Entity\Castor\Instances;


use App\Entity\Castor\Record;
use DateTime;
use Exception;

class ReportInstance extends Instance
{
    /** @var string */
    private $name;

    /** @var string */
    private $reportName;

    /** @var string */
    private $reportId;

    /** @var string */
    private $status;

    /** @var string */
    private $parentId;

    /** @var string */
    private $parentType;

    /**
     * ReportInstance constructor.
     * @param string $id
     * @param string $name
     * @param string $reportName
     * @param string $reportId
     * @param string $status
     * @param string $parentId
     * @param string $parentType
     * @param Record $record
     * @param DateTime $createdOn
     */
    public function __construct(string $id, string $name, string $reportName, string $reportId, string $status, string $parentId, string $parentType, Record $record, DateTime $createdOn)
    {
        parent::__construct($id, $record, $createdOn);
        $this->name = $name;
        $this->reportName = $reportName;
        $this->reportId = $reportId;
        $this->status = $status;
        $this->parentId = $parentId;
        $this->parentType = $parentType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReportName(): string
    {
        return $this->reportName;
    }

    /**
     * @return string
     */
    public function getReportId(): string
    {
        return $this->reportId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getParentId(): string
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getParentType(): string
    {
        return $this->parentType;
    }

    /**
     * @param array<mixed> $data
     * @param Record $record
     * @return ReportInstance
     * @throws Exception
     */
    public static function fromData(array $data, Record $record): ReportInstance
    {
        return new ReportInstance(
            $data['id'],
            $data['name'],
            $data['report_name'],
            $data['_embedded']['report']['id'],
            $data['status'],
            $data['parent_id'],
            $data['parent_type'],
            $record,
            new DateTime($data['created_on'])
        );
    }}