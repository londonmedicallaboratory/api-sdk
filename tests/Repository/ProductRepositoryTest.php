<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductRepositoryTest extends TestCase
{
    private HttpClientInterface $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = HttpClient::create();
    }

    public function testClient(): void
    {
        $responses = [];
        $client = $this->client;
        for ($i = 0; $i < 5; ++$i) {
            $uri = "https://http2.akamai.com/demo/tile-$i.png";
            $responses[] = $client->request('GET', $uri);
        }
        foreach ($responses as $response) {
            $response->getContent();
        }
    }
}
