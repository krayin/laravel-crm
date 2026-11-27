<?php

use Webkul\Admin\DataGrids\Activity\ActivityDataGrid;

/**
 * `core()->formatDate()` rendered the month and the meridiem through `Carbon::format()`, which
 * always produces English words, so every admin locale saw "30 Nov 2026 11:00AM". The datagrid
 * columns and the calendar preview both read that helper.
 */
it('renders the month and the meridiem in the current locale', function () {
    app()->setLocale('en');

    $english = core()->formatDate('2026-11-30 11:00:00');

    app()->setLocale('es');

    $spanish = core()->formatDate('2026-11-30 11:00:00');

    expect($english)->toBe('30 Nov 2026 11:00AM');

    expect($spanish)
        ->not->toBe($english)
        ->toContain('nov')
        ->not->toContain('Nov')
        ->not->toContain('AM');
});

it('keeps a numeric format untouched by the locale', function () {
    app()->setLocale('es');

    expect(core()->formatDate('2026-11-30 11:00:00', 'd-m-Y'))->toBe('30-11-2026');
});

it('translates the date columns of the activities datagrid', function () {
    $row = (object) ['schedule_from' => '2026-11-30 11:00:00'];

    $grid = new ActivityDataGrid;

    $grid->prepareColumns();

    $column = collect($grid->getColumns())
        ->first(fn ($column) => $column->getIndex() === 'schedule_from');

    expect($column)->not->toBeNull();

    $render = fn () => ($column->getClosure())($row);

    app()->setLocale('en');

    expect($render())->toBe('30 Nov 2026 11:00AM');

    app()->setLocale('es');

    expect($render())
        ->toContain('nov')
        ->not->toContain('Nov')
        ->not->toContain('AM');
});

it('has a translated calendar label in every shipped locale', function () {
    $keys = [
        'drag-to-change-start-time',
        'drag-to-change-end-time',
        'untitled-activity',
        'new-time-range',
    ];

    $loader = app('translator')->getLoader();

    foreach (array_keys(config('app.available_locales')) as $locale) {
        $lines = $loader->load($locale, 'app', 'admin');

        foreach ($keys as $key) {
            /**
             * The translator falls back to English for a missing key, so asking `trans()` would
             * pass even when the locale never defined it. The raw file has to carry the key.
             */
            expect(array_key_exists($key, $lines['activities']['calendar'] ?? []))
                ->toBeTrue("admin::app.activities.calendar.{$key} is missing for {$locale}");
        }
    }
});
