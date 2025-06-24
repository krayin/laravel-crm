@props([
    'customAttributes' => [],
    'entity'           => null,
    'allowEdit'        => false,
    'url'              => null,
])

<div class="flex flex-col gap-1">
    @foreach ($customAttributes as $attribute)
        @if (view()->exists($typeView = 'admin::components.attributes.view.' . $attribute->type))
            <div class="grid grid-cols-[1fr_2fr] items-center gap-1">
                <div class="label dark:text-white">{{ $attribute->name }}</div>

                <div class="font-medium dark:text-white">

                    @if($attribute->code === 'rate')
                        @php

                        $rate=isset($entity) ? $entity[$attribute->code] : 0;

                        @endphp

                        <div class="person-rate-show">
                            <input disabled type="radio" <?php echo $rate == 5 ? "checked" : ""; ?> id="star5" name="rate"
                                   value="5"/>
                            <label for="star5" title="text">5 stars</label>
                            <input disabled type="radio" <?php echo $rate == 4 ? "checked" : ""; ?> id="star4" name="rate"
                                   value="4"/>
                            <label for="star4" title="text">4 stars</label>
                            <input disabled type="radio" <?php echo $rate == 3 ? "checked" : ""; ?> id="star3" name="rate"
                                   value="3"/>
                            <label for="star3" title="text">3 stars</label>
                            <input disabled type="radio" <?php echo $rate == 2 ? "checked" : ""; ?> id="star2" name="rate"
                                   value="2"/>
                            <label for="star2" title="text">2 stars</label>
                            <input disabled type="radio" <?php echo $rate == 1 ? "checked" : ""; ?> id="star1" name="rate"
                                   value="1"/>
                            <label for="star1" title="text">1 star</label>
                        </div>

                    @else
                        @include ($typeView, [
                                                'attribute' => $attribute,
                                                'value'     => isset($entity) ? $entity[$attribute->code] : null,
                                                'allowEdit' => $allowEdit,
                                                'url'       => $url,
                                            ])

                    @endif


                </div>
            </div>
        @endif
    @endforeach
</div>
