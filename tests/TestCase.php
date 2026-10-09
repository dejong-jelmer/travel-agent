<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Fail when the callback lazy loads a relation, naming every relation it lazy loaded. Laravel only reports this for
     * models fetched together with others, so the callback needs to load at least two of them.
     */
    protected function assertNoLazyLoading(callable $callback): void
    {
        $violations = [];
        Model::preventLazyLoading();
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) use (&$violations) {
            $violations[] = $model::class.'::'.$relation;
        });

        try {
            $callback();
        } finally {
            Model::preventLazyLoading(false);
            Model::handleLazyLoadingViolationUsing(null);
        }

        $this->assertSame([], array_values(array_unique($violations)), 'Relations were lazy loaded.');
    }
}
