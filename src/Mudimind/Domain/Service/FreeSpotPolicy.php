<?php

namespace App\Mudimind\Domain\Service;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Stateless availability policy. Knows the salon's working hours and how to
 * derive free slots from a set of already-booked intervals. Operates purely on
 * date/time primitives so both the read side (free-spot listing) and the write
 * side (booking conflict check) can reuse it.
 *
 * A "busy" interval is an array{start: DateTimeInterface, end: DateTimeInterface}.
 */
class FreeSpotPolicy
{
    /** Salon opens at 09:00 and closes at 18:00, every day. */
    public const WORK_START_HOUR = 9;
    public const WORK_END_HOUR = 18;

    /** Candidate slots are offered on this minute grid (09:00, 09:15, …). */
    private const SLOT_STEP_MINUTES = 15;

    /**
     * Free slots of the given duration on the given day, in chronological order.
     *
     * @param list<array{start: DateTimeInterface, end: DateTimeInterface}> $busy
     *
     * @return list<array{start: DateTimeImmutable, end: DateTimeImmutable}>
     */
    public function freeSlots(DateTimeInterface $day, int $durationMinutes, array $busy): array
    {
        [$windowStart, $windowEnd] = $this->workingWindow($day);

        $step = new DateInterval('PT' . self::SLOT_STEP_MINUTES . 'M');
        $duration = new DateInterval('PT' . $durationMinutes . 'M');

        $slots = [];
        for ($start = $windowStart; ; $start = $start->add($step)) {
            $end = $start->add($duration);
            if ($end > $windowEnd) {
                break;
            }
            if ($this->isAvailable($start, $end, $busy)) {
                $slots[] = ['start' => $start, 'end' => $end];
            }
        }

        return $slots;
    }

    /**
     * Whether [start, end) fits inside working hours and overlaps no busy interval.
     *
     * @param list<array{start: DateTimeInterface, end: DateTimeInterface}> $busy
     */
    public function isAvailable(DateTimeInterface $start, DateTimeInterface $end, array $busy): bool
    {
        if (!$this->isWithinWorkingHours($start, $end)) {
            return false;
        }

        foreach ($busy as $interval) {
            // Half-open intervals overlap iff start < otherEnd AND end > otherStart.
            if ($start < $interval['end'] && $end > $interval['start']) {
                return false;
            }
        }

        return true;
    }

    public function isWithinWorkingHours(DateTimeInterface $start, DateTimeInterface $end): bool
    {
        [$windowStart, $windowEnd] = $this->workingWindow($start);

        return $start >= $windowStart
            && $end <= $windowEnd
            && $start->format('Y-m-d') === $end->format('Y-m-d');
    }

    /**
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable}
     */
    private function workingWindow(DateTimeInterface $day): array
    {
        $base = DateTimeImmutable::createFromInterface($day);

        return [
            $base->setTime(self::WORK_START_HOUR, 0),
            $base->setTime(self::WORK_END_HOUR, 0),
        ];
    }
}