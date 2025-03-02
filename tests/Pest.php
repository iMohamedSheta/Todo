<?php

namespace Tests;

pest()->extend(TestCase::class)->in('Feature');

it('can run a test', function (): void {
    $this->assertTrue(true);
});
