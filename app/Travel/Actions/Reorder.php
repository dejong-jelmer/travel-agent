<?php

namespace App\Travel\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Reorder
{
    /**
     * Rewrite the positions of all children of one parent in the given order, the first getting 0. Works for every
     * positioned child: the legs of a journey or an arrival route, and the journeys and stays of a booking.
     *
     * @param  HasOneOrMany<*, *, *>  $children  For example $booking->journeys() or $journey->legs().
     * @param  list<int>  $ids  The id of every child, each exactly once.
     *
     * @throws ValidationException When the ids are not exactly the children: a partial list is refused, not guessed at.
     */
    public function handle(HasOneOrMany $children, array $ids): void
    {
        DB::transaction(function () use ($children, $ids) {
            // Lock the parent, as nextPosition() does, so a reorder waits for a create or another reorder of the same
            // children: it checks the ids against the children as they are, and two reorders cannot deadlock on rows
            // they update in a different order.
            $parent = $children->getParent();
            $parent->newQuery()->whereKey($parent->getKey())->lockForUpdate()->first();

            $current = $children->get()->keyBy(fn (Model $child) => $child->getKey());

            $given = $ids;
            sort($given);

            if ($given !== $current->keys()->sort()->values()->all()) {
                throw ValidationException::withMessages([
                    'ids' => 'Geef elk onderdeel precies één keer op, in de nieuwe volgorde.',
                ]);
            }

            foreach ($ids as $position => $id) {
                $current[$id]->update(['position' => $position]);
            }
        });
    }
}
