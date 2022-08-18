<?php
declare(strict_types=1);

namespace App\Entity\Data\Log;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Logs executed (and failed) SPARQL queries that any users make against an RDF distribution.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SparqlQueryLogRepository")
 * @ORM\Table(name="log_sparql_query")
 */
final class SparqlQueryLog
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * The distribution against which the query was made.
     * @ORM\Column(name="distribution_id", type="text", length=40, nullable=false)
     */
    private string $distributionId;

    /**
     * The User ID of the user who triggered the Query.
     * @ORM\Column(name="user_id", type="text", length=40, nullable=false)
     */
    private string $userId;

    /**
     * The email address of the user who triggered the query. We save this next to the user ID for traceability
     * purposes: users can change their email address associated with the user ID.
     * @ORM\Column(name="user_email", type="text", length=255, nullable=false)
     */
    private string $userEmail;

    /**
     * The timestamp when the query was executed.
     * @ORM\Column(name="queried_on", type="datetime", nullable=false)
     */
    private DateTimeImmutable $queriedOn;

    /**
     * The actual query that was executed against the RDF store.
     * @ORM\Column(name="sparql_query", type="text", nullable=false)
     */
    private string $sparqlQuery;

    /**
     * The number of results that were returned from the RDF store.
     * @ORM\Column(name="result_count", type="integer", nullable=false)
     */
    private int $resultCount;

    /**
     * Any errors that were generated during the query.
     * @ORM\Column(name="error", type="text", nullable=true)
     */
    private ?string $error;

    private function __construct(
        string $distributionId,
        string $userId,
        string $userEmail,
        string $sparqlQuery,
        int $resultCount = 0,
        ?string $error = null
    ) {
        $this->distributionId = $distributionId;
        $this->queriedOn = new DateTimeImmutable();
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->sparqlQuery = $sparqlQuery;
        $this->resultCount = $resultCount;
        $this->error = $error;
    }

    public static function successfulQuery(
        string $distributionId,
        string $userId,
        string $userEmail,
        string $sparqlQuery,
        int $resultCount
    ): SparqlQueryLog {
        return new self($distributionId, $userId, $userEmail, $sparqlQuery, $resultCount, null);
    }

    public static function failedQuery(
        string $distributionId,
        string $userId,
        string $userEmail,
        string $sparqlQuery,
        string $error
    ): SparqlQueryLog {
        return new self($distributionId, $userId, $userEmail, $sparqlQuery, 0, $error);
    }
}
