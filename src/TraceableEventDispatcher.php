<?php

declare(strict_types=1);

namespace Codin\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class TraceableEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $dispatchedEvents;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->dispatchedEvents = [];
    }

    /**
     * @inherit
     */
    public function dispatch(object $event): object
    {
        $eventName = get_class($event);
        $start = microtime(true);
        $result = $this->eventDispatcher->dispatch($event);
        $duration = microtime(true) - $start;
        $context = [
            'duration' => $duration,
            'event' => $eventName,
        ];
        $this->dispatchedEvents[] = $context;
        $this->logger->info($eventName, $context);
        return $result;
    }

    /**
     * Return wrapped event dispatcher
     */
    public function getWrappedEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Events that have been called
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }
}
