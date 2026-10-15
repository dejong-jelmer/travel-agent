<?php

namespace App\Travel\Actions\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

trait AssignsNextPosition
{
    /**
     * One past the highest position among the children, or 0 for the first child; gaps left by deletes stay. Locks
     * the parent row until the transaction ends, so two creates at the same time cannot get the same position: call
     * it inside a transaction.
     *
     * @param  HasOneOrMany<*, *, *>  $children
     */
    private function nextPosition(HasOneOrMany $children): int
    {
        $parent = $children->getParent();
        $parent->newQuery()->whereKey($parent->getKey())->lockForUpdate()->first();

        $highest = (clone $children)->reorder()->max('position');

        return $highest === null ? 0 : (int) $highest + 1;
    }
}
