<?php

namespace App\Tests;

use App\Services\TheBrainDateService;
use App\Services\TheBrainService;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class DatesTest extends TestCase
{
    private TheBrainDateService $theBrainDateService;

    public function setUp(): void
    {
        $client = new Client();

        $theBrainService = new TheBrainService(
            $_ENV['THEBRAIN_API_URL'],
            $_ENV['THEBRAIN_API_KEY'],
            $_ENV['BRAIN_ID'],
            $client
        );

        $weekTagIds = [
            $_ENV['MONDAY_TAG_ID'],
            $_ENV['TUESDAY_TAG_ID'],
            $_ENV['WEDNESDAY_TAG_ID'],
            $_ENV['THIRSDAY_TAG_ID'],
            $_ENV['FRIDAY_TAG_ID'],
            $_ENV['SUNDAY_TAG_ID'],
            $_ENV['SATURDAY_TAG_ID'],
        ];

        $this->theBrainDateService = new TheBrainDateService(
            $theBrainService,
            $_ENV['DATE_TYPE_THOUGHT_ID'],
            $weekTagIds
        );
    }

    public function testCreateDates()
    {
        $thoughts = $this
            ->theBrainDateService
            ->createDateThoughts(6, 2026, $_ENV['SOURCE_THOUGHT_ID']);

        $this->assertIsArray($thoughts);
        $this->assertCount(30, $thoughts);
    }
}
