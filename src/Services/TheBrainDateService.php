<?php

namespace App\Services;

use App\Exceptions\InvalidBrainException;
use App\Exceptions\TheBrainException;
use App\Services\Enum\ThoughtAccessType;
use App\Services\Enum\ThoughtKind;
use App\Services\Enum\ThoughtRelation;
use GuzzleHttp\Exception\GuzzleException;

class TheBrainDateService
{
    private const MONTHS = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];

    /**
     * @param array<string> $weekTagIds
     */
    public function __construct(
        private readonly TheBrainService $theBrainService,
        private readonly string $dateTypeThoughtId,
        private readonly array $weekTagIds,
    ) {

    }

    /**
     * @throws InvalidBrainException
     * @throws GuzzleException
     * @throws TheBrainException
     *
     * @return array<string>
     */
    public function createDateThoughts(
        int $month,
        int $year,
        string $parentNodeId
    ): array {
        $createdThoughts = [];

        try {
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $previousThoughtId = null;
            for ($day = 1; $day <= $days; $day++) {
                $thoughtName = $day . ' ' . self::MONTHS[$month] . ' ' . $year;

                $thoughtId = $this->theBrainService->createThought(
                    $thoughtName,
                    ThoughtKind::NORMAL,
                    ThoughtAccessType::PUBLIC,
                    null === $previousThoughtId ? null : $previousThoughtId,
                    null === $previousThoughtId ? null : ThoughtRelation::JUMP
                );

                $dayNumber = date('N', strtotime(sprintf('%s-%s-%s', $day, $month, $year)));

                $weekDayTagId = $this->weekTagIds[$dayNumber - 1];

                $this->theBrainService->linkThoughts($this->dateTypeThoughtId, $thoughtId, ThoughtRelation::CHILD);
                $this->theBrainService->linkThoughts($weekDayTagId, $thoughtId, ThoughtRelation::CHILD);
                $this->theBrainService->linkThoughts($parentNodeId, $thoughtId,  ThoughtRelation::CHILD);

                $createdThoughts[] = $thoughtId;
                $previousThoughtId = $thoughtId;
            }
        } catch (GuzzleException | InvalidBrainException $e) {
            foreach ($createdThoughts as $thoughtId) {
                $this->theBrainService->deleteThought($thoughtId);
            }

            throw new TheBrainException($e->getMessage(), $e->getCode(), $e);
        }

        return $createdThoughts;
    }
}
