<?php

namespace PhilKra\Helper;

use PhilKra\Exception\Timer\AlreadyRunningException;
use PhilKra\Exception\Timer\NotStartedException;
use PhilKra\Exception\Timer\NotStoppedException;

/**
 * Timer for Duration tracing
 */
class Timer
{
    /**
     * Starting Timestamp
     *
     * @var int
     */
    private $startedOn;

    /**
     * Ending Timestamp
     *
     * @var int
     */
    private $stoppedOn;

    public function __construct(float $startTime = null)
    {
        $this->startedOn = $startTime;
    }

    /**
     * Get the Event's Timestamp Epoch in Micro
     *
     * @return int
     */
    public function getNow(): int
    {
        return $this->microSeconds();
    }

    /**
     * Start the Timer
     *
     * @param float|null $startTime
     * @throws AlreadyRunningException
     */
    public function start(float $startTime = null): void
    {
        if (null !== $this->startedOn) {
            throw new AlreadyRunningException();
        }
        $this->startedOn = (null !== $startTime) ? $startTime : $this->milliSeconds();
    }

    /**
     * Stop the Timer
     *
     * @throws NotStartedException
     */
    public function stop(): void
    {
        if (null === $this->startedOn) {
            throw new NotStartedException();
        }

        $this->stoppedOn = $this->milliSeconds();
    }

    /**
     * Get the elapsed Duration of this Timer in Miliseconds
     *
     * @throws \PhilKra\Exception\Timer\NotStoppedException
     *
     * @return int
     */
    public function getDuration(): int
    {
        if (null === $this->stoppedOn) {
            throw new NotStoppedException();
        }

        return $this->stoppedOn - $this->startedOn;
    }

    /**
     * Get the current elapsed Interval of the Timer in Miliseconds
     *
     * @return float
     * @throws NotStoppedException
     *
     * @throws NotStartedException
     */
    public function getElapsed(): float
    {
        if (null === $this->startedOn) {
            throw new NotStartedException();
        }

        $time = (null === $this->stoppedOn) ?
            $this->milliSeconds() - $this->startedOn :
            $this->getDuration();
        return $time >= 0 ? $time : 0;
    }

    /**
     * @return float
     */
    private function milliSeconds(): float
    {
        $mt = explode(' ', microtime());

        return ((float) $mt[1]) * 1000 + ((int) round($mt[0] * 1000));
    }

    /**
     * @return int
     */
    private function microSeconds(): int
    {
        $mt = explode(' ', microtime());

        return ((int) $mt[1]) * 1000000 + ((int) round($mt[0] * 1000000));
    }
}
