<?php

declare(strict_types=1);

namespace Codin\Events\Contracts;

use Psr\EventDispatcher\ListenerProviderInterface;

interface EventDispatcher
{
    public function registerListener(ListenerProviderInterface $listener): void;

    public function getListeners(): array;

    public function getListenersForEvent(object $event): iterable;
}
