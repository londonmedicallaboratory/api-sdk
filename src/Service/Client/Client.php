<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function json_decode;

class Client
{
    private HttpClientInterface $client;

    public function __construct(private string $baseUrl, string $username, string $password)
    {
        $this->client = HttpClient::createForBaseUri(
            $baseUrl,
            [
                'auth_basic' => [$username, $password],
            ]
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function get(string $url): array
    {
        $url = $this->baseUrl . $url;
        $content = $this->client->request('GET', $url)->getContent();

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
