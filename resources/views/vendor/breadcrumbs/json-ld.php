<?php

use Illuminate\Support\Facades\Request;

$json = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [],
];

foreach ($breadcrumbs as $i => $breadcrumb) {
    $json['itemListElement'][] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => [
            '@id'   => $breadcrumb->url ?: Request::fullUrl(),
            'name'  => $breadcrumb->title,
            'image' => $breadcrumb->image ?? null,
        ],
    ];
}
?>
<script type="application/ld+json"><?= json_encode($json) ?></script>
