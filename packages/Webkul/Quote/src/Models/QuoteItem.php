<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Quote\Contracts\QuoteItem as QuoteItemContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuoteItem extends Model implements QuoteItemContract
{
    use BelongsToTenant;
    protected $table = 'quote_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'quantity',
        'price',
        'coupon_code',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total',
        'product_id',
        'quote_id',
    ];

    /**
     * Get the quote record associated with the quote item.
     */
    public function quote()
    {
        return $this->belongsTo(QuoteProxy::modelClass());
    }
}
