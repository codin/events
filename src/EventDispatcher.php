<?php

declare(strict_types=1);

namespace Codin\Events;

use AppendIterator;
use ArrayIterator;
use Iterator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements Contracts\EventDispatcher, EventDispatcherInterface
{
    /**
     * @var ArrayIterator<int, ListenerProviderInterface>
     */
    protected $listeners;

    /**
     * @param null|ArrayIterator<int, ListenerProviderInterface> $listeners
     */
    public function __construct(?ArrayIterator $listeners = null)
    {
        $this->listeners = null === $listeners ? new ArrayIterator() : $listeners;
    }

    /**
     * Register a new listener
     *
     * @param ListenerProviderInterface $listener
     */
    public function registerListener(ListenerProviderInterface $listener): void
    {
        $this->listeners->append($listener);
    }

    /**
     * Return registered listeners
     *
     * @return array<ListenerProviderInterface>
     */
    public function getListeners(): array
    {
        return $this->listeners->getArrayCopy();
    }

    /**
     * Get listeners for event as a single iterator
     *
     * @param object $event
     * @return iterable[callable]
     */
    public function getListenersForEvent(object $event): iterable
    {
        $appendIterator = new AppendIterator();

        foreach ($this->listeners as $listener) {
            $iterable = $listener->getListenersForEvent($event);

            if (\is_array($iterable)) {
                $iterable = new ArrayIterator($iterable);
            }

            if ($iterable instanceof Iterator) {
                $appendIterator->append($iterable);
            }
        }

        return $appendIterator;
    }

    /**
     * @inherit
     * @see https://github.com/phly/phly-event-dispatcher
     */
    public function dispatch(object $event): object
    {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->getListenersForEvent($event) as $callable) {
            if (!\is_callable($callable)) {
                throw Exceptions\ListenerError::notCallable($callable, $event);
            }

            $callable($event);

            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}
