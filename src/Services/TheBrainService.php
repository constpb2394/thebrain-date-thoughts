<?php

namespace App\Services;

use App\Services\Enum\ThoughtAccessType;
use App\Services\Enum\ThoughtKind;
use App\Services\Enum\ThoughtRelation;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TheBrainService
{
    private const CONTENT_TYPE = 'application/json';

    public function __construct(
        private readonly string $apiUrl,
        private readonly string $apiKey,
        private readonly string $brainId,
        private ?HttpClient $client,
    ) {
        $this->client = $client ?? new HttpClient();
    }

    /**
     * @throws GuzzleException
     */
    public function createThought(
        string $name,
        ThoughtKind $thoughtKind,
        ThoughtAccessType $thoughtAccessType,
        ?string $sourceThoughtId = null,
        ?ThoughtRelation $thoughtRelation = null,
    ): string {
        $body = [
            'name' => $name,
            'kind' => $thoughtKind,
            'acType' => $thoughtAccessType,
        ];

        if (null !== $sourceThoughtId && null !== $thoughtRelation) {
            $body['sourceThoughtId'] = $sourceThoughtId;
            $body['relation'] = $thoughtRelation;
        }

        $url = "{$this->apiUrl}/thoughts/{$this->brainId}";

        $response =  $this->client->request('POST', $url, [
            RequestOptions::HEADERS => [
                'Content-Type' => self::CONTENT_TYPE,
                'Accept' => self::CONTENT_TYPE,
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            RequestOptions::JSON => $body,
        ]);

        $content = $response->getBody()->getContents();
        $json = json_decode($content, true);

        return $json['id'];
    }

    /**
     * @throws GuzzleException
     */
    public function linkThoughts(
        string $thoughtIdA,
        string $thoughtIdB,
        ThoughtRelation $thoughtRelation,
        ?string $name = null,
    ): string {
        $body = [
            'thoughtIdA' => $thoughtIdA,
            'thoughtIdB' => $thoughtIdB,
            'relation' => $thoughtRelation,
        ];

        if (null !== $name) {
            $body['name'] = $name;
        }

        $url = "{$this->apiUrl}/links/{$this->brainId}";

        $response =  $this->client->request('POST', $url, [
            RequestOptions::HEADERS => [
                'Content-Type' => self::CONTENT_TYPE,
                'Accept' => self::CONTENT_TYPE,
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            RequestOptions::JSON => $body,
        ]);

        $content = $response->getBody()->getContents();
        $json = json_decode($content, true);

        return $json['id'];
    }

    /**
     * @throws GuzzleException
     */
    public function deleteThought(string $thoughtId): void {
        $url = "{$this->apiUrl}/thoughts/{$this->brainId}/{$thoughtId}";

        $this->client->request('DELETE', $url, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]
        ]);
    }
}
