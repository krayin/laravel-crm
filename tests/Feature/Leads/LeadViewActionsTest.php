<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Product as LeadProduct;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Webkul\Product\Models\Product;
use Webkul\Quote\Models\Quote;
use Webkul\Quote\Models\QuoteItem;
use Webkul\Quote\Repositories\QuoteRepository;
use Webkul\Tag\Models\Tag;

uses(DatabaseTransactions::class);

function leadViewActionsLead(): Lead
{
    $pipeline = securityTestPipeline();

    return Lead::create([
        'title' => 'Lead view actions '.uniqid(),
        'description' => 'Exercises the actions on a lead page.',
        'lead_value' => 1000,
        'status' => 1,
        'user_id' => getDefaultAdmin()->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->first()->id,
        'lead_source_id' => Source::query()->first()->id,
        'lead_type_id' => Type::query()->first()->id,
    ]);
}

function leadViewActionsPerson(): Person
{
    return Person::create([
        'name' => 'Lead view actions contact '.uniqid(),
        'emails' => [['value' => uniqid().'@example.invalid', 'label' => 'work']],
        'user_id' => getDefaultAdmin()->id,
    ]);
}

function leadViewActionsQuote(string $subject): Quote
{
    return Quote::create([
        'subject' => $subject,
        'user_id' => getDefaultAdmin()->id,
        'person_id' => leadViewActionsPerson()->id,
        'expired_at' => now()->addWeek(),
        'sub_total' => 0,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'adjustment_amount' => 0,
        'grand_total' => 0,
    ]);
}

function leadViewActionsProduct(string $suffix = ''): Product
{
    return Product::create([
        'sku' => 'LVA-'.uniqid().$suffix,
        'name' => 'Lead view actions product '.$suffix,
        'price' => 100,
        'quantity' => 10,
    ]);
}

it('opens a lead page', function () {
    $lead = leadViewActionsLead();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.leads.view', $lead->id))
        ->assertOk()
        ->assertSee($lead->title, false);
});

it('moves a lead to another stage', function () {
    $lead = leadViewActionsLead();

    $stage = $lead->pipeline->stages()->create([
        'name' => 'Lead view actions stage',
        'code' => 'lva-'.uniqid(),
        'probability' => 100,
        'sort_order' => 99,
    ]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->putJson(route('admin.leads.stage.update', $lead->id), [
            'lead_pipeline_stage_id' => $stage->id,
        ])
        ->assertOk();

    expect($lead->refresh()->lead_pipeline_stage_id)->toBe($stage->id);
});

it('updates a single attribute of a lead', function () {
    $lead = leadViewActionsLead();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->putJson(route('admin.leads.attributes.update', $lead->id), [
            'lead_value' => 5000,
        ])
        ->assertOk();

    expect((float) $lead->refresh()->lead_value)->toBe(5000.0);
});

it('attaches and detaches a tag', function () {
    $lead = leadViewActionsLead();

    $tag = Tag::create([
        'name' => 'Lead view actions '.uniqid(),
        'color' => '#FF0000',
        'user_id' => getDefaultAdmin()->id,
    ]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->postJson(route('admin.leads.tags.attach', $lead->id), ['tag_id' => $tag->id])
        ->assertOk();

    expect($lead->refresh()->tags->pluck('id'))->toContain($tag->id);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.leads.tags.detach', $lead->id), ['tag_id' => $tag->id])
        ->assertOk();

    expect($lead->refresh()->tags->pluck('id'))->not->toContain($tag->id);
});

it('adds and removes a product', function () {
    $lead = leadViewActionsLead();

    $product = leadViewActionsProduct('add');

    $this->actingAs(getDefaultAdmin(), 'user')
        ->putJson(route('admin.leads.product.add', $lead->id), [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 150,
        ])
        ->assertOk();

    $leadProduct = LeadProduct::where('lead_id', $lead->id)
        ->where('product_id', $product->id)
        ->first();

    expect($leadProduct)->not->toBeNull();

    expect((float) $leadProduct->amount)->toBe(300.0);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.leads.product.remove', $lead->id), [
            'product_id' => $product->id,
        ])
        ->assertOk();

    expect(
        LeadProduct::where('lead_id', $lead->id)->where('product_id', $product->id)->exists()
    )->toBeFalse();
});

it('detaches a quote from a lead', function () {
    $lead = leadViewActionsLead();

    $quote = leadViewActionsQuote('Lead view actions quote');

    $lead->quotes()->attach($quote->id);

    expect($lead->refresh()->quotes->pluck('id'))->toContain($quote->id);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.leads.quotes.delete', $quote->id))
        ->assertOk();

    expect($lead->refresh()->quotes->pluck('id'))->not->toContain($quote->id);

    expect(Quote::find($quote->id))->not->toBeNull();
});

it('answers with not found when detaching a quote that does not exist', function () {
    $missingId = (Quote::max('id') ?? 0) + 1000;

    $this->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.leads.quotes.delete', $missingId))
        ->assertNotFound();
});

it('detaches a quote whose id does not name a lead', function () {
    $lead = leadViewActionsLead();

    $quote = leadViewActionsQuote('Quote with no matching lead id');

    expect(Lead::find($quote->id))->toBeNull();

    $lead->quotes()->attach($quote->id);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.leads.quotes.delete', $quote->id))
        ->assertOk();

    expect($lead->refresh()->quotes->pluck('id'))->not->toContain($quote->id);
});

it('deletes a lead', function () {
    $lead = leadViewActionsLead();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.leads.delete', $lead->id))
        ->assertOk();

    expect(Lead::find($lead->id))->toBeNull();
});

it('gives a lead and its quote the same products when they are linked', function () {
    $lead = leadViewActionsLead();

    $quote = leadViewActionsQuote('Lead view actions sync');

    $quoteProducts = collect(range(1, 4))->map(fn ($i) => leadViewActionsProduct("q$i"));

    foreach ($quoteProducts as $product) {
        QuoteItem::create([
            'quote_id' => $quote->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'price' => 10,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total' => 10,
        ]);
    }

    $leadProducts = collect(range(1, 2))->map(fn ($i) => leadViewActionsProduct("l$i"));

    foreach ($leadProducts as $product) {
        LeadProduct::create([
            'lead_id' => $lead->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 5,
            'amount' => 10,
        ]);
    }

    $repository = app(QuoteRepository::class);

    $sync = new ReflectionMethod($repository, 'syncLeadProducts');

    $sync->setAccessible(true);

    $sync->invoke($repository, $quote, $lead->id);

    expect($quote->refresh()->items)->toHaveCount(6);

    expect(LeadProduct::where('lead_id', $lead->id)->count())->toBe(6);

    $sync->invoke($repository, $quote, $lead->id);

    expect($quote->refresh()->items)->toHaveCount(6);

    expect(LeadProduct::where('lead_id', $lead->id)->count())->toBe(6);
});
