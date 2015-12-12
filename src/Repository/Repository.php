<?php

namespace BoundedContext\Repository;

use BoundedContext\ValueObject\Uuid;
use BoundedContext\Contracts\Sourced\Aggregate;
use BoundedContext\Contracts\Sourced\Log;
use \BoundedContext\Projection\AggregateCollections;

class Repository implements \BoundedContext\Contracts\Sourced\Repository
{
    private $log;
    private $projection;
    private $aggregate;

    public function __construct(
        Log $log,
        AggregateCollections\Projection $projection,
        Aggregate $aggregate
    )
    {
        $this->log = $log;
        $this->projection = $projection;
        $this->aggregate = $aggregate;
    }

    public function get(Uuid $id)
    {
        $aggregate_class = get_class($this->aggregate);
        $state = clone $this->aggregate->state();

        return new $aggregate_class(
            $id,
            $state,
            $this->projection->get($id)
        );
    }

    public function save(Aggregate $aggregate)
    {
        $this->log->append_collection(
            $aggregate->changes()
        );

        $aggregate->flush();
    }
}
