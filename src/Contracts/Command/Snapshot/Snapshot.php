<?php namespace BoundedContext\Contracts\Command\Snapshot;

use BoundedContext\Contracts\Schema\Schema;
use BoundedContext\Contracts\Snapshot\Snapshot as SnapshotContract;
use EventSourced\ValueObject\Contracts\ValueObject\Identifier;

interface Snapshot extends SnapshotContract
{
    /**
     * Gets the type id of the Event.
     *
     * @return Identifier
     */
    public function type_id();
    
    /**
     * Gets the current Schema.
     *
     * @return Schema
     */
    public function schema();
}