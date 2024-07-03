<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.general',
        'info' => 'admin::app.configuration.general',
        'sort' => 1,
    ], [
        'key'    => 'general.locale_settings',
        'name'   => 'admin::app.configuration.locale-settings',
        'info'   => 'admin::app.configuration.general',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'locale',
                'title'         => 'admin::app.configuration.locale',
                'type'          => 'select',
                'options'   => 'Webkul\Core\Core@locales'
            ],
        ],
    ], [
        'key'    => 'general.header_offer',
        'name'   => 'Header Offer',
        'info'   => 'Header Offer Configuration',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'title',
                'title'      => 'Offer Title',
                'type'       => 'text',
                'default'    => 'Get UPTO 40% OFF on your 1st order',
                'validation' => 'required|max:100|min:3',
            ], [
                'name'       => 'redirection_title',
                'title'      => 'Redirection Title',
                'type'       => 'text',
                'default'    => 'SHOP NOW',
                'validation' => 'required|max:100|min:3',
            ], [
                'name'       => 'redirection_link',
                'title'      => 'Redirection Link',
                'type'       => 'text',
                'validation' => 'required|max:100|min:3|url',
            ],
        ],
    ], [
        'key'    => 'general.custom_scripts',
        'name'   => 'Custom Scripts',
        'info'   => 'admin::app.configuration.index.general.custom-scripts.title-info',
        'sort'   => 2,
        'fields' => [
            [
                'name'          => 'custom_css',
                'title'         => 'Custom Css',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'custom_javascript',
                'title'         => 'Custom Javascript',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ], 
     /**
     * Catalog.
     */
   [
        'key'  => 'catalog',
        'name' => 'Catalog',
        'info' => 'admin::app.configuration.index.catalog.info',
        'icon' => 'settings/product.svg',
        'sort' => 1,
    ], [
        'key'    => 'catalog.settings',
        'name'   => 'Settings',
        'info'   => 'admin::app.configuration.index.catalog.settings.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'compare_option',
                'title'         => 'Compare Options',
                'type'          => 'country',
                'validation'    => 'required',
                'default'       => 1,
            ], [
                'name'          => 'image_search',
                'title'         => 'Image Search Options',
                'type'          => 'state',
                'validation'    => 'required',
                'default'       => 1,
            ],
            [
                'name'          => 'cart_options',
                'title'         => 'Show Cart Options',
                'type'          => 'text',
                'validation'    => 'required',
            ],
            [
                'name'          => 'admin_logo',
                'title'         => 'Admin Logo',
                'type'          => 'image',
            ],
            [
                'name'          => 'shop_logo',
                'title'         => 'Shop Logo',
                'type'          => 'image',
            ],
        ],
    ], [
        'key'    => 'catalog.search',
        'name'   => 'Search',
        'info'   => 'admin::app.configuration.index.catalog.search.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'engine',
                'title'   => 'Search Engine',
                'type'    => 'select',
                'default' => 'database',
                'options' => [
                    [
                        'title' => 'Database',
                        'value' => 'database',
                    ], [
                        'title' => 'Elastic Search',
                        'value' => 'elastic',
                    ],
                ],
            ], [
                'name'    => 'admin_mode',
                'title'   => 'Admin Mode',
                'info'    => 'Admin Mode-info',
                'type'    => 'select',
                'default' => 'database',
                'options' => [
                    [
                        'title' => 'Database',
                        'value' => 'database',
                    ], [
                        'title' => 'Elastic Search',
                        'value' => 'elastic',
                    ],
                ],
            ], [
                'name'    => 'storefront_mode',
                'title'   => 'Storefront Mode',
                'info'    => 'Storefront Mode-info',
                'type'    => 'select',
                'default' => 'database',
                'options' => [
                    [
                        'title' => 'Database',
                        'value' => 'database',
                    ], [
                        'title' => 'Elastic Search',
                        'value' => 'elastic',
                    ],
                ],
            ], [
                'name'       => 'min_query_length',
                'title'      => 'Search Minimum Query length',
                'info'       => 'Search Minimum Query length-info',
                'type'       => 'number',
                'validation' => 'numeric',
                'default'    => '0',
            ], [
                'name'       => 'max_query_length',
                'title'      => 'Max Search Query',
                'info'       => 'Max Search Query-info',
                'type'       => 'number',
                'validation' => 'numeric',
                'default'    => '1000',
            ],
        ],
    ], [
        'key'    => 'catalog.product_view_page',
        'name'   => 'Product View Page',
        'info'   => 'Product View Page-info',
        'sort'   => 2,
        'fields' => [
            [
                'name'       => 'no_of_related_products',
                'title'      => 'Allow No of related products',
                'type'       => 'number',
                'validation' => 'integer|min:0',
            ], [
                'name'       => 'no_of_up_sells_products',
                'title'      => 'Allow No of up sells products',
                'type'       => 'text',
                'validation' => 'integer|min:0',
            ],
        ],
    ], [
        'key'    => 'catalog.storefront',
        'name'   => 'Store Front Title',
        'info'   => 'Store Front Title-info',
        'sort'   => 4,
        'fields' => [
            [
                'name'          => 'mode',
                'title'         => 'Default List Mode',
                'type'          => 'select',
                'options'       => [
                    [
                        'title' => 'Grid',
                        'value' => 'grid',
                    ], [
                        'title' => 'List',
                        'value' => 'list',
                    ],
                ],
                'channel_based' => true,
            ], [
                'name'          => 'products_per_page',
                'title'         => 'Product Per Page',
                'type'          => 'text',
                'info'          => 'admin::app.configuration.index.catalog.storefront.comma-separated',
                'channel_based' => true,
            ], [
                'name'          => 'sort_by',
                'title'         => 'Sort By',
                'type'          => 'select',
                'options'       => [
                    [
                        'title' => 'A-Z',
                        'value' => 'name-asc',
                    ], [
                        'title' => 'Z-A',
                        'value' => 'name-desc',
                    ], [
                        'title' => 'Latest First',
                        'value' => 'created_at-desc',
                    ], [
                        'title' => 'Oldest First',
                        'value' => 'created_at-asc',
                    ], [
                        'title' => 'Cheapest First',
                        'value' => 'price-asc',
                    ], [
                        'title' => 'Expensive First',
                        'value' => 'price-desc',
                    ],
                ],
                'channel_based' => true,
            ], [
                'name'  => 'buy_now_button_display',
                'title' => 'Buy Now Button Display',
                'type'  => 'boolean',
            ],
        ],
    ],
];