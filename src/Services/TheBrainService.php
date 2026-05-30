<?php

namespace App\Services;

use App\Exceptions\InvalidBrainException;
use App\Services\Enum\ThoughtAccessType;
use App\Services\Enum\ThoughtKind;
use App\Services\Enum\ThoughtRelation;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TheBrainService
{
    private const CONTENT_TYPE = 'application/json';

    private ?string $brainId = null;

    public function __construct(
        private readonly HttpClient $client,
        private readonly string $apiUrl,
        private readonly string $apiKey,
    ) {

    }

    public function setBrainId(string $brainId): void
    {
        $this->brainId = $brainId;
    }

    /**
     * @throws InvalidBrainException
     * @throws GuzzleException
     */
    public function createThought(
        string $name,
        ThoughtKind $thoughtKind,
        ThoughtAccessType $thoughtAccessType,
        ?string $sourceThoughtId = null,
        ?ThoughtRelation $thoughtRelation = null,
    ): string {
        if (null === $this->brainId) {
            throw new InvalidBrainException();
        }

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
     * @throws InvalidBrainException
     * @throws GuzzleException
     */
    public function linkThoughts(
        string $thoughtIdA,
        string $thoughtIdB,
        ThoughtRelation $thoughtRelation,
        ?string $name = null,
    ): string {
        if (null === $this->brainId) {
            throw new InvalidBrainException();
        }

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
     * @throws InvalidBrainException
     * @throws GuzzleException
     */
    public function deleteThought(string $thoughtId) {
        if (null === $this->brainId) {
            throw new InvalidBrainException();
        }


        $url = "{$this->apiUrl}/thoughts/{$this->brainId}/{$thoughtId}";

        $this->client->request('DELETE', $url, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]
        ]);
    }
}
