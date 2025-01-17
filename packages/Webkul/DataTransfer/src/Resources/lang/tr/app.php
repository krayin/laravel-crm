<?php

return [
    'importers' => [
        'persons' => [
            'title' => 'Kişiler',

            'validation' => [
                'errors' => [
                    'duplicate-email' => 'E-posta: \'%s\' içe aktarma dosyasında birden fazla kez bulundu.',
                    'duplicate-phone' => 'Telefon: \'%s\' içe aktarma dosyasında birden fazla kez bulundu.',
                    'email-not-found' => 'E-posta: \'%s\' sistemde bulunamadı.',
                ],
            ],
        ],

        'products' => [
            'title' => 'Ürünler',

            'validation' => [
                'errors' => [
                    'sku-not-found' => 'Belirtilen SKU\'ya sahip ürün bulunamadı.',
                ],
            ],
        ],

        'leads' => [
            'title' => 'Müşteri Adayları',

            'validation' => [
                'errors' => [
                    'id-not-found' => 'ID: \'%s\' sistemde bulunamadı.',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => '"%s" numaralı sütunların başlıkları boş.',
            'column-name-invalid'  => 'Geçersiz sütun adları: "%s".',
            'column-not-found'     => 'Gerekli sütunlar bulunamadı: %s.',
            'column-numbers'       => 'Sütun sayısı başlıktaki satır sayısına karşılık gelmiyor.',
            'invalid-attribute'    => 'Başlık geçersiz öznitelikler içeriyor: "%s".',
            'system'               => 'Beklenmeyen bir sistem hatası oluştu.',
            'wrong-quotes'         => 'Doğru olmayan tırnak işaretleri kullanıldı.',
        ],
    ],
];
