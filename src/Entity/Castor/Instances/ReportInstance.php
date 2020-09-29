<?php
declare(strict_types=1);

namespace App\Entity\Castor\Instances;

use App\Entity\Castor\Record;
use DateTime;
use Exception;

class ReportInstance extends Instance
{
    private string $name;

    private string $reportName;

    private string $reportId;

    private string $status;

    private string $parentId;

    private string $parentType;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getReportName(): string
    {
        return $this->reportName;
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }

    public function getParentType(): string
    {
        return $this->parentType;
    }

    /**
     * @param array<mixed> $data
     *
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
    }
}
