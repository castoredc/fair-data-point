<?php
declare(strict_types=1);

namespace App\Tests\unit\Model\Castor;

use App\Entity\Encryption\SensitiveDataString;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Factory\Castor\CastorUserFactory;
use App\Factory\Castor\InstituteFactory;
use App\Factory\Castor\RecordFactory;
use App\Model\Castor\ApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;
use function json_encode;

final class ApiClientTest extends TestCase
{
    private MockHandler $mockHandler;

    private ApiClient $apiClient;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();

        $this->apiClient = new ApiClient(
            $this->createMock(RecordFactory::class),
            $this->createMock(InstituteFactory::class),
            $this->createMock(CastorUserFactory::class),
            new Client(['handler' => HandlerStack::create($this->mockHandler)]),
        );

        $this->apiClient->setServer('https://castor.example.org');
    }

    public function testAuthRequestsAndStoresAnAccessToken(): void
    {
        $this->mockHandler->append(new Response(200, [], (string) json_encode(['access_token' => 'token-123'])));

        $this->apiClient->auth(new SensitiveDataString('client-id'), new SensitiveDataString('client-secret'));

        $authRequest = $this->mockHandler->getLastRequest();
        self::assertSame('POST', $authRequest->getMethod());
        self::assertSame('https://castor.example.org/oauth/token', (string) $authRequest->getUri());

        // The token must be used as Bearer token on subsequent requests.
        $this->mockHandler->append(new Response(200, [], (string) json_encode(['_embedded' => ['study' => []]])));
        $this->apiClient->getStudies();

        $studyRequest = $this->mockHandler->getLastRequest();
        self::assertSame('Bearer token-123', $studyRequest->getHeaderLine('Authorization'));
    }

    public function testGetStudiesParsesTheResponse(): void
    {
        $this->apiClient->setToken('token-123');
        $this->mockHandler->append(new Response(200, [], (string) json_encode([
            '_embedded' => [
                'study' => [
                    ['study_id' => 'study-1', 'name' => 'Study One', 'slug' => 'study-one'],
                    ['study_id' => 'study-2', 'name' => 'Study Two', 'slug' => 'study-two'],
                ],
            ],
        ])));

        $studies = $this->apiClient->getStudies();

        self::assertCount(2, $studies);
        self::assertSame('study-1', $studies[0]->getSourceId());
        self::assertSame('Study One', $studies[0]->getName());
        self::assertSame('study-two', $studies[1]->getSlug());
    }

    /** @return array<string, array{int, class-string<Throwable>}> */
    public static function errorStatusCodes(): array
    {
        return [
            'unauthorized' => [401, SessionTimedOut::class],
            'forbidden' => [403, NoAccessPermission::class],
            'not found' => [404, NotFound::class],
            'server error' => [500, ErrorFetchingCastorData::class],
        ];
    }

    /** @param class-string<Throwable> $expectedException */
    #[DataProvider('errorStatusCodes')]
    public function testRequestErrorsAreMappedToDomainExceptions(int $statusCode, string $expectedException): void
    {
        $this->apiClient->setToken('token-123');
        $this->mockHandler->append(new Response($statusCode));

        $this->expectException($expectedException);

        $this->apiClient->getStudies();
    }
}
