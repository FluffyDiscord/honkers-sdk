<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Telemetry;

use FluffyDiscord\Honkers\DTO\ChatOrder;
use FluffyDiscord\Honkers\DTO\SiteCredentials;
use FluffyDiscord\Honkers\Exception\TelemetryException;
use FluffyDiscord\Honkers\Telemetry\TelemetryClient;
use FluffyDiscord\Honkers\Tests\Unit\Fixtures\CapturingHttpClient;
use FluffyDiscord\Honkers\Tests\Unit\Fixtures\TransportFailure;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TelemetryClientTest extends TestCase
{
    public function testALinkVisitIsPostedWithTheSiteKeyBearer(): void
    {
        $httpClient = new CapturingHttpClient(new Response(202));
        $client = $this->createClient($httpClient);

        $client->reportLinkVisit($this->createCredentials(),'click-123', 'https://shop.test/product/clipper');

        $request = $httpClient->lastRequest;
        self::assertNotNull($request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://honkers.test/api/v1/catalog/link-visits', (string) $request->getUri());
        self::assertSame('Bearer pk_site.ingest-secret', $request->getHeaderLine('Authorization'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertSame(
            ['clickId' => 'click-123', 'pageUrl' => 'https://shop.test/product/clipper'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testAnOrderIsPostedWithTheSiteKeyBearer(): void
    {
        $httpClient = new CapturingHttpClient(new Response(202));
        $client = $this->createClient($httpClient);

        $client->reportOrder($this->createCredentials(),new ChatOrder('click-123', '000042', 123450, 'CZK'));

        $request = $httpClient->lastRequest;
        self::assertNotNull($request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://honkers.test/api/v1/catalog/chat-orders', (string) $request->getUri());
        self::assertSame('Bearer pk_site.ingest-secret', $request->getHeaderLine('Authorization'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame(
            ['clickId' => 'click-123', 'orderNumber' => '000042', 'revenue' => 123450, 'currency' => 'CZK'],
            json_decode((string) $request->getBody(), true),
        );
    }

    /**
     * @return iterable<string, array{int, string, ?string}>
     */
    public static function provideRejections(): iterable
    {
        yield 'unauthorized' => [401, json_encode(['error' => ['code' => 'invalid_token']]), 'invalid_token'];
        yield 'unauthorized without body' => [401, '', null];
        yield 'unknown click id' => [404, json_encode(['error' => ['code' => 'not_found']]), 'not_found'];
        yield 'throttled' => [429, json_encode(['error' => ['code' => 'rate_limited']]), 'rate_limited'];
        yield 'success that is not accepted' => [200, '', null];
    }

    #[DataProvider('provideRejections')]
    public function testANonAcceptedResponseThrowsWithTheStatusAndBackendCode(
        int     $statusCode,
        string  $body,
        ?string $backendErrorCode,
    ): void {
        $client = $this->createClient(new CapturingHttpClient(new Response($statusCode, [], $body)));

        try {
            $client->reportLinkVisit($this->createCredentials(),'click-123', 'https://shop.test/');
            self::fail('Expected a TelemetryException.');
        } catch (TelemetryException $exception) {
            self::assertSame($statusCode, $exception->getStatusCode());
            self::assertSame($backendErrorCode, $exception->getBackendErrorCode());
        }
    }

    public function testAnOrderRejectionThrows(): void
    {
        $response = new Response(404, [], json_encode(['error' => ['code' => 'not_found']]));
        $client = $this->createClient(new CapturingHttpClient($response));

        $this->expectException(TelemetryException::class);

        $client->reportOrder($this->createCredentials(),new ChatOrder('forged', '000042', 500, 'JPY'));
    }

    public function testATransportFailureIsWrapped(): void
    {
        $failure = new TransportFailure('connection refused');
        $client = $this->createClient(new CapturingHttpClient(new Response(202), $failure));

        try {
            $client->reportLinkVisit($this->createCredentials(),'click-123', 'https://shop.test/');
            self::fail('Expected a TelemetryException.');
        } catch (TelemetryException $exception) {
            self::assertSame(0, $exception->getStatusCode());
            self::assertSame($failure, $exception->getPrevious());
        }
    }

    private function createClient(CapturingHttpClient $httpClient): TelemetryClient
    {
        $factory = new Psr17Factory();

        return new TelemetryClient($httpClient, $factory, $factory, 'https://honkers.test/');
    }

    private function createCredentials(): SiteCredentials
    {
        return new SiteCredentials('pk_site', 'ingest-secret');
    }
}
