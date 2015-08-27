<?php

namespace BoundedContext\Aggregate;

use BoundedContext\Contracts\Event;
use BoundedContext\ValueObject\Uuid;
use BoundedContext\Collection\Collection;

abstract class AbstractAggregate
{
    protected $id;
    protected $state;
    protected $changes;

    public function __construct(Uuid $id, Collection $items)
    {
        $this->id = $id;
        $this->changes = new Collection();

        $this->state = $this->generate();

        foreach($items as $item)
        {
            $this->apply(
                $item->event()
            );
        }

        $this->flush();
    }

    protected abstract function generate();

    private function check_event_belongs(Event $event)
    {
        if(!$event->id()->equals($this->id))
        {
            throw new \Exception("Event [".get_class($event)."] ".$event->id()->toString()." does not belong to Aggregate ".$this->id()->toString().".");
        }
    }

    public function id()
    {
        return $this->id;
    }

    public function state()
    {
        return $this->state;
    }

    public function version()
    {
        return $this->state->version();
    }

    public function changes()
    {
        return $this->changes;
    }
    
    public function flush()
    {
        $this->changes = new Collection();
    }

    protected function apply(Event $event)
    {
        $this->check_event_belongs($event);

        $this->state->apply($event);
        $this->changes->append($event);
    }

}
