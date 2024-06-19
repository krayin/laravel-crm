<?php

return [
    'seeders' => [
        'attributes' => [
            'address'             => 'Adres',
            'adjustment-amount'   => 'Düzenleme Tutarı',
            'billing-address'     => 'Fatura Adresi',
            'contact-numbers'     => 'İletişim Numaraları',
            'description'         => 'Açıklama',
            'discount-amount'     => 'İndirim Tutarı',
            'discount-percent'    => 'İndirim Yüzdesi',
            'emails'              => 'E-postalar',
            'expected-close-date' => 'Beklenen Kapanış Tarihi',
            'expired-at'          => 'Son Kullanma Tarihi',
            'grand-total'         => 'Genel Toplam',
            'lead-value'          => 'Müşteri Değeri',
            'name'                => 'İsim',
            'organization'        => 'Kuruluş',
            'person'              => 'Kişi',
            'price'               => 'Fiyat',
            'quantity'            => 'Miktar',
            'sales-owner'         => 'Satış Sahibi',
            'shipping-address'    => 'Teslimat Adresi',
            'sku'                 => 'SKU',
            'source'              => 'Kaynak',
            'sub-total'           => 'Ara Toplam',
            'subject'             => 'Konu',
            'tax-amount'          => 'Vergi Tutarı',
            'title'               => 'Başlık',
            'type'                => 'Tür',
        ],

        'email' => [
            'activity-created'      => 'Etkinlik oluşturuldu',
            'activity-modified'     => 'Etkinlik değiştirildi',
            'date'                  => 'Tarih',
            'new-activity'          => 'Yeni bir etkinliğiniz var, detaylar aşağıda',
            'new-activity-modified' => 'Yeni bir etkinlik değiştirildi, detaylar aşağıda',
            'participants'          => 'Katılımcılar',
            'title'                 => 'Başlık',
            'type'                  => 'Tür',
        ],

        'lead' => [
            'pipeline' => [
                'default' => 'Varsayılan Boru Hattı',

                'pipeline-stages' => [
                    'follow-up'   => 'Takip',
                    'lost'        => 'Kaybedildi',
                    'negotiation' => 'Müzakere',
                    'new'         => 'Yeni',
                    'prospect'    => 'Potansiyel',
                    'won'         => 'Kazandı',
                ],
            ],

            'source' => [
                'direct'    => 'Doğrudan',
                'email'     => 'E-posta',
                'phone'     => 'Telefon',
                'web'       => 'Web',
                'web-form'  => 'Web Formu',
            ],

            'type' => [
                'existing-business' => 'Mevcut İş',
                'new-business'      => 'Yeni İş',
            ],
        ],

        'user' => [
            'role' => [
                'administrator-role' => 'Yönetici Rolü',
                'administrator'      => 'Yönetici',
            ],
        ],

        'workflow' => [
            'email-to-participants-after-activity-updation' => 'Etkinlik güncellendikten sonra katılımcılara e-posta gönder',
            'email-to-participants-after-activity-creation' => 'Etkinlik oluşturulduktan sonra katılımcılara e-posta gönder',
        ],
    ],
];