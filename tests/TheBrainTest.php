<?php

namespace App\Tests;

use App\Services\Enum\ThoughtAccessType;
use App\Services\Enum\ThoughtKind;
use App\Services\Enum\ThoughtRelation;
use App\Services\TheBrainService;
use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class TheBrainTest extends TestCase
{
    private TheBrainService $service;

    private static string $thoughtA;
    private static string $thoughtB;

    private string $sourceThoughtId;
    private string $defaultDateTypeId;

    public function setUp(): void
    {
        $client = new Client();

        $this->service = new TheBrainService(
            $client,
            $_ENV['THEBRAIN_API_URL'],
            $_ENV['THEBRAIN_API_KEY']
        );

        $this->service->setBrainId($_ENV['TEST_BRAIN_ID']);

        $this->sourceThoughtId = $_ENV['SOURCE_THOUGHT_ID'];
        $this->defaultDateTypeId = $_ENV['DATE_TYPE_THOUGHT_ID'];
    }

    public function testCreateThought()
    {
        $thoughtId = $this->service->createThought(
            'Test Thought',
            ThoughtKind::NORMAL,
            ThoughtAccessType::PUBLIC
        );

        $this->assertIsString($thoughtId);

        self::$thoughtA = $thoughtId;
    }

    public function testCreateThoughtWithSource() {
        $thoughtId = $this->service->createThought(
            'Test Thought With Source',
            ThoughtKind::NORMAL,
            ThoughtAccessType::PUBLIC,
            $this->sourceThoughtId,
            ThoughtRelation::CHILD

        );

        $this->assertIsString($thoughtId);

        self::$thoughtB = $thoughtId;
    }

    public function testCreateThoughtWithSourceType() {
        $thoughtId = $this->service->createThought(
            'Test Thought With Source Type',
            ThoughtKind::NORMAL,
            ThoughtAccessType::PUBLIC,
            $this->sourceThoughtId,
            ThoughtRelation::CHILD

        );

        $this->assertIsString($thoughtId);
    }

    #[Depends('testCreateThought')]
    #[Depends('testCreateThoughtWithSource')]
    public function testLinkThoughtsAsJump()
    {
        $linkId = $this->service->linkThoughts(
            self::$thoughtA,
            self::$thoughtB,
            ThoughtRelation::JUMP,
        );

        $this->assertIsString($linkId);
    }

    #[Depends('testCreateThought')]
    #[Depends('testCreateThoughtWithSource')]
    public function testLinkThoughtsAsChild()
    {
        $linkIdA = $this->service->linkThoughts(
            $this->defaultDateTypeId,
            self::$thoughtA,
            ThoughtRelation::CHILD,
        );

        $linkIdB = $this->service->linkThoughts(
            $this->defaultDateTypeId,
            self::$thoughtB,
            ThoughtRelation::CHILD,
        );

        $this->assertIsString($linkIdA);
        $this->assertIsString($linkIdB);
    }
}
