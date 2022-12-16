# Event Dispatcher

![version](https://img.shields.io/github/v/tag/codin/events)
![workflow](https://img.shields.io/github/workflow/status/codin/events/Composer)
![license](https://img.shields.io/github/license/codin/events)

PSR-14 Compatible event dispatcher

Usage

```php
class MyListener implements \Psr\EventDispatcher\ListenerProviderInterface
{
    public function getListenersForEvent(object $event) : iterable
    {
        yield static function (MyEvent $event) {
            echo "$event->message\n";
        };
    }
}

class MyEvent {
    public string $message = 'Hello World';
}

$dispatcher = new Codin\Events\EventDispatcher();
$dispatcher->registerListener(new MyListener());
$dispatcher->dispatch(new MyEvent());
```
