<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Models\Attribute;
use Webkul\Automation\Helpers\Entity;
use Webkul\Automation\Helpers\Entity\Activity;

/**
 * Ensures that building the workflow/email-template placeholder list costs a fixed
 * number of queries regardless of how many custom attributes exist, and that repeated
 * calls within one request incur zero additional queries (issue #2658).
 */
uses(DatabaseTransactions::class);

/**
 * Seed N custom lead attributes: half select/multiselect with options, half text.
 */
function placeholderTestAddLeadAttributes(int $count = 10): void
{
    for ($i = 1; $i <= $count; $i++) {
        if ($i % 2 === 1) {
            $type = 'select';
        } elseif ($i % 4 === 2) {
            $type = 'multiselect';
        } else {
            $type = 'text';
        }

        $attribute = Attribute::create([
            'code' => 'ph_test_'.$i,
            'name' => 'Placeholder Test '.$i,
            'type' => $type,
            'entity_type' => 'leads',
            'lookup_type' => null,
            'is_required' => 0,
            'is_unique' => 0,
            'is_user_defined' => 1,
            'quick_add' => 0,
            'validation' => null,
        ]);

        if (in_array($type, ['select', 'multiselect'])) {
            for ($j = 1; $j <= 3; $j++) {
                $attribute->options()->create([
                    'name' => 'Option '.$j,
                    'sort_order' => $j,
                ]);
            }
        }
    }
}

/**
 * Run a callable and return the number of database queries it issued.
 */
function placeholderTestCountQueries(callable $callback): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();
    $callback();
    $n = count(DB::getQueryLog());
    DB::disableQueryLog();

    return $n;
}

/**
 * Resolve a fresh Entity instance, bypassing any scoped/singleton binding.
 */
function placeholderTestFreshEntity(): Entity
{
    app()->forgetInstance(Entity::class);

    return app(Entity::class);
}

/**
 * Independently rebuild the expected placeholder array the same way the source code does,
 * without going through the Entity helper classes under test.
 */
function placeholderTestExpected(): array
{
    $skipAttributes = ['textarea', 'image', 'file', 'address'];
    $placeholders = [];

    foreach (config('workflows.trigger_entities') as $key => $entity) {
        if ($key === 'activities') {
            // Activity uses a hard-coded attribute list; delegate to the helper which is not touched.
            $placeholders[] = app(Activity::class)->getEmailTemplatePlaceholders($entity);

            continue;
        }

        $menuItems = [];

        foreach (Attribute::where('entity_type', $key)->orderBy('id')->get() as $attribute) {
            if (in_array($attribute->type, $skipAttributes)) {
                continue;
            }

            $menuItems[] = [
                'text' => $attribute->name,
                'value' => '{%'.$key.'.'.$attribute->code.'%}',
            ];
        }

        $placeholders[] = [
            'text' => $entity['name'],
            'menu' => $menuItems,
        ];
    }

    return $placeholders;
}

it('builds the placeholder list with the same number of queries whatever the attribute count', function () {
    $before = placeholderTestCountQueries(fn () => placeholderTestFreshEntity()->getEmailTemplatePlaceholders());

    placeholderTestAddLeadAttributes(10);

    $after = placeholderTestCountQueries(fn () => placeholderTestFreshEntity()->getEmailTemplatePlaceholders());

    expect($after)->toBe($before);
});

it('builds the placeholder list once per request however many times it is asked for', function () {
    placeholderTestAddLeadAttributes(10);

    app()->forgetInstance(Entity::class);

    // Warm-up: first build populates the memo.
    app('\Webkul\Automation\Helpers\Entity')->getEmailTemplatePlaceholders();

    // All subsequent calls on the same scoped instance must cost 0 queries.
    expect(placeholderTestCountQueries(function () {
        foreach (range(1, 5) as $_) {
            app('\Webkul\Automation\Helpers\Entity')->getEmailTemplatePlaceholders();
        }
    }))->toBe(0);

    // The backslash-prefixed string used by Blade views must resolve to the same object.
    expect(app('\Webkul\Automation\Helpers\Entity'))->toBe(app(Entity::class));
});

it('returns the same placeholders as before for stock and custom attributes', function () {
    // Stock: output matches independent rebuild.
    $result = placeholderTestFreshEntity()->getEmailTemplatePlaceholders();
    $expected = placeholderTestExpected();

    expect($result)->toEqual($expected);
    expect(json_encode($result))->toBe(json_encode($expected));

    // With 10 extra lead attributes: output still matches.
    placeholderTestAddLeadAttributes(10);

    $result = placeholderTestFreshEntity()->getEmailTemplatePlaceholders();
    $expected = placeholderTestExpected();

    expect($result)->toEqual($expected);
    expect(json_encode($result))->toBe(json_encode($expected));

    // Confirm the custom attribute placeholder is present.
    $leadsBlock = collect($result)->firstWhere('text', 'Leads');
    expect(collect($leadsBlock['menu'])->pluck('value')->contains('{%leads.ph_test_1%}'))->toBeTrue();
});

it('does not keep placeholders between requests', function () {
    // Build once so the memo is filled.
    app()->forgetInstance(Entity::class);
    app(Entity::class)->getEmailTemplatePlaceholders();

    // Add a new attribute and simulate a new request by flushing scoped instances.
    placeholderTestAddLeadAttributes(1);
    app()->forgetScopedInstances();

    $result = app(Entity::class)->getEmailTemplatePlaceholders();
    $leadsBlock = collect($result)->firstWhere('text', 'Leads');

    expect(collect($leadsBlock['menu'])->pluck('value')->contains('{%leads.ph_test_1%}'))->toBeTrue();
});
