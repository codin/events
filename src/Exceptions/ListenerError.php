<?php

declare(strict_types=1);

namespace Codin\Events\Exceptions;

use Exception;

class ListenerError extends Exception
{
    /**
     * @param mixed $callable
     * @param object $event
     */
    public static function notCallable($callable, object $event): self
    {
        return new self(sprintf('Listener returned a non-callable "%s" for event "%s"', \gettype($callable), \get_class($event)));
    }
}
