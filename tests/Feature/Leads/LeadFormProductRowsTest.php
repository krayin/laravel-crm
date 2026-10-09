<?php

use Illuminate\Support\Facades\Validator;
use Webkul\Admin\Http\Requests\LeadForm;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

/**
 * A product row that arrives without a `product_id` key used to pass LeadForm validation
 * (`sometimes` skips `required` for a missing key) and then failed as an HTTP 500 on the
 * NOT NULL `lead_products.product_id` column.
 */
beforeEach(function () {
    $attributes = Mockery::mock(AttributeRepository::class);

    $attributes->shouldReceive('scopeQuery')->andReturnSelf();
    $attributes->shouldReceive('get')->andReturn(collect());

    $this->rules = (new LeadForm($attributes, Mockery::mock(AttributeValueRepository::class)))->rules();
});

it('rejects a product row submitted without a product_id key', function () {
    $validator = Validator::make([
        'products' => [
            'product_0' => ['name' => 'Widget', 'price' => 10, 'quantity' => 1],
        ],
    ], $this->rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toContain('products.product_0.product_id');
});

it('rejects a product row submitted with an empty product_id', function () {
    $validator = Validator::make([
        'products' => [
            'product_0' => ['product_id' => null, 'name' => 'Widget', 'price' => 10, 'quantity' => 1],
        ],
    ], $this->rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toContain('products.product_0.product_id');
});

it('does not require product rows when none are submitted', function () {
    expect(Validator::make([], $this->rules)->passes())->toBeTrue();
});
