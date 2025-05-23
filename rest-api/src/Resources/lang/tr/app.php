<?php

return [
    'common' => [
        'auth' => [
            'login' => [
                'success' => 'Giriş başarılı.',
                'logout'  => 'Çıkış başarılı.',
            ],
        ],

        'resource-not-found'    => 'İstenen kaynak bulunamadı.',
        'forbidden-error'       => 'Bu kaynağa erişim izniniz yok.',
        'unauthenticated'       => 'Kimliğiniz doğrulanmadı. Devam etmek için lütfen giriş yapın.',
        'internal-server-error' => 'Sunucuda beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyin.',
    ],

    'products' => [
        'create-success'           => 'Ürün başarıyla oluşturuldu.',
        'updated-success'          => 'Ürün başarıyla güncellendi.',
        'delete-success'           => 'Ürün başarıyla silindi.',
        'delete-failed'            => 'Ürün silme işlemi başarısız oldu.',
        'inventory-create-success' => 'Envanter başarıyla kaydedildi.',
    ],

    'leads' => [
        'create-success'  => 'Potansiyel müşteri başarıyla oluşturuldu.',
        'updated-success' => 'Potansiyel müşteri başarıyla güncellendi.',
        'delete-success'  => 'Potansiyel müşteri başarıyla silindi.',
        'delete-failed'   => 'Potansiyel müşteri silme işlemi başarısız oldu.',
        'no-valid-files'  => 'Geçerli dosya bulunamadı.',

        'view' => [
            'tags' => [
                'create-success' => 'Etiket başarıyla eklendi.',
                'delete-success' => 'Etiket başarıyla kaldırıldı.',
            ],

            'quotes' => [
                'create-success' => 'Teklif başarıyla eklendi.',
                'delete-success' => 'Teklif başarıyla kaldırıldı.',
            ],
        ],
    ],

    'quotes' => [
        'create-success'  => 'Teklif başarıyla oluşturuldu.',
        'update-success'  => 'Teklif başarıyla güncellendi.',
        'delete-success'  => 'Teklif başarıyla silindi.',
        'delete-failed'   => 'Teklif silme işlemi başarısız oldu.',
        'saved-to-draft'  => 'Teklif taslak olarak kaydedildi.',
    ],

    'mail' => [
        'create-success' => 'E-posta başarıyla oluşturuldu.',
        'update-success' => 'E-posta başarıyla güncellendi.',
        'delete-success' => 'E-posta başarıyla silindi.',
        'delete-failed'  => 'E-posta silme işlemi başarısız oldu.',
        'saved-to-draft' => 'E-posta taslak olarak kaydedildi.',

        'view' => [
            'tags' => [
                'create-success' => 'Etiket başarıyla eklendi.',
                'delete-success' => 'Etiket başarıyla kaldırıldı.',
            ],
        ],
    ],

    'activities' => [
        'create-success' => 'Etkinlik başarıyla oluşturuldu.',
        'update-success' => 'Etkinlik başarıyla güncellendi.',
        'delete-success' => 'Etkinlik başarıyla silindi.',
        'delete-failed'  => 'Etkinlik silme işlemi başarısız oldu.',
    ],

    'contacts' => [
        'persons' => [
            'create-success'  => 'Kişi başarıyla oluşturuldu.',
            'update-success'  => 'Kişi başarıyla güncellendi.',
            'delete-success'  => 'Kişi başarıyla silindi.',
            'delete-failed'   => 'Kişi silme işlemi başarısız oldu.',

            'view' => [
                'tags' => [
                    'create-success' => 'Etiket başarıyla eklendi.',
                    'delete-success' => 'Etiket başarıyla kaldırıldı.',
                ],
            ],
        ],

        'organizations' => [
            'create-success'  => 'Organizasyon başarıyla oluşturuldu.',
            'update-success'  => 'Organizasyon başarıyla güncellendi.',
            'delete-success'  => 'Organizasyon başarıyla silindi.',
            'delete-failed'   => 'Organizasyon silme işlemi başarısız oldu.',
        ],
    ],

    'settings' => [
        'tags' => [
            'create-success' => 'Etiket başarıyla oluşturuldu.',
            'update-success' => 'Etiket başarıyla güncellendi.',
            'delete-success' => 'Etiket başarıyla silindi.',
            'delete-failed'  => 'Etiket silme işlemi başarısız oldu.',
        ],

        'web-forms' => [
            'create-success'  => 'Web formu başarıyla oluşturuldu.',
            'updated-success' => 'Web formu başarıyla güncellendi.',
            'delete-success'  => 'Web formu başarıyla silindi.',
            'delete-failed'   => 'Web formu silme işlemi başarısız oldu.',
        ],

        'attributes' => [
            'create-success'    => 'Özellik başarıyla oluşturuldu.',
            'update-success'    => 'Özellik başarıyla güncellendi.',
            'destroy-success'   => 'Özellik başarıyla silindi.',
            'delete-failed'     => 'Özellik silinirken hata oluştu.',
            'user-define-error' => 'Kullanıcı tanımlı özellik silinemez.',
        ],

        'groups' => [
            'create-success'  => 'Grup başarıyla oluşturuldu.',
            'update-success'  => 'Grup başarıyla güncellendi.',
            'destroy-success' => 'Grup başarıyla silindi.',
            'delete-failed'   => 'Grup silme işlemi başarısız oldu.',
        ],

        'marketing' => [
            'events' => [
                'create-success'  => 'Pazarlama etkinliği başarıyla oluşturuldu.',
                'update-success'  => 'Pazarlama etkinliği başarıyla güncellendi.',
                'destroy-success' => 'Pazarlama etkinliği başarıyla silindi.',
                'delete-failed'   => 'Pazarlama etkinliği silinemedi.',
            ],

            'campaigns' => [
                'create-success'  => 'Pazarlama kampanyası başarıyla oluşturuldu.',
                'update-success'  => 'Pazarlama kampanyası başarıyla güncellendi.',
                'destroy-success' => 'Pazarlama kampanyası başarıyla silindi.',
                'delete-failed'   => 'Pazarlama kampanyası silinemedi.',
            ],
        ],

        'roles' => [
            'create-success' => 'Rol başarıyla oluşturuldu.',
            'update-success' => 'Rol başarıyla güncellendi.',
            'delete-success' => 'Rol başarıyla silindi.',
            'delete-failed'  => 'Rol silme işlemi başarısız oldu.',
        ],

        'users' => [
            'create-success'      => 'Kullanıcı başarıyla oluşturuldu.',
            'updated-success'     => 'Kullanıcı başarıyla güncellendi.',
            'delete-success'      => 'Kullanıcı başarıyla silindi.',
            'delete-failed'       => 'Kullanıcı silme işlemi başarısız oldu.',
            'last-delete-error'   => 'En az bir kullanıcı gereklidir.',
            'mass-delete-success' => 'Seçili kullanıcılar başarıyla silindi.',
            'mass-delete-failed'  => 'Seçili kullanıcılar silme işlemi başarısız oldu.',
            'mass-update-success' => 'Seçili kullanıcılar başarıyla güncellendi.',
            'mass-update-failed'  => 'Seçili kullanıcılar güncelleme işlemi başarısız oldu.',
        ],

        'pipelines' => [
            'create-success'       => 'Pipeline başarıyla oluşturuldu.',
            'updated-success'      => 'Pipeline başarıyla güncellendi.',
            'delete-success'       => 'Pipeline başarıyla silindi.',
            'default-delete-error' => 'Varsayılan pipeline silinemez.',
        ],

        'sources' => [
            'create-success' => 'Kaynak başarıyla oluşturuldu.',
            'update-success' => 'Kaynak başarıyla güncellendi.',
            'delete-success' => 'Kaynak başarıyla silindi.',
            'delete-failed'  => 'Kaynak silme işlemi başarısız oldu.',
        ],

        'types' => [
            'create-success' => 'Tür başarıyla oluşturuldu.',
            'update-success' => 'Tür başarıyla güncellendi.',
            'delete-success' => 'Tür başarıyla silindi.',
            'delete-failed'  => 'Tür silme işlemi başarısız oldu.',
        ],

        'email-templates' => [
            'create-success' => 'E-posta şablonu başarıyla oluşturuldu.',
            'update-success' => 'E-posta şablonu başarıyla güncellendi.',
            'delete-success' => 'E-posta şablonu başarıyla silindi.',
            'delete-failed'  => 'E-posta şablonu silme işlemi başarısız oldu.',
        ],

        'workflows' => [
            'create-success' => 'İş akışı başarıyla oluşturuldu.',
            'update-success' => 'İş akışı başarıyla güncellendi.',
            'delete-success' => 'İş akışı başarıyla silindi.',
            'delete-failed'  => 'İş akışı silme işlemi başarısız oldu.',
        ],

        'warehouses' => [
            'create-success' => 'Depo başarıyla oluşturuldu.',
            'update-success' => 'Depo başarıyla güncellendi.',
            'delete-success' => 'Depo başarıyla silindi.',
            'delete-failed'  => 'Depo silme işlemi başarısız oldu.',

            'view' => [
                'locations' => [
                    'create-success' => 'Konum başarıyla oluşturuldu.',
                    'update-success' => 'Konum başarıyla güncellendi.',
                    'delete-success' => 'Konum başarıyla silindi.',
                    'delete-failed'  => 'Konum silme işlemi başarısız oldu.',
                ],

                'tags' => [
                    'create-success' => 'Etiket başarıyla eklendi.',
                    'delete-success' => 'Etiket başarıyla kaldırıldı.',
                ],
            ],
        ],

        'webhooks' => [
            'create-success' => 'Webhook başarıyla oluşturuldu.',
            'update-success' => 'Webhook başarıyla güncellendi.',
            'delete-success' => 'Webhook başarıyla silindi.',
            'delete-failed'  => 'Webhook silme işlemi başarısız oldu.',
        ],

        'data-transfer' => [
            'imports' => [
                'create-success'    => 'İçe aktarma başarıyla oluşturuldu.',
                'delete-failed'     => 'İçe aktarma silme işlemi beklenmedik şekilde başarısız oldu.',
                'delete-success'    => 'İçe aktarma başarıyla silindi.',
                'not-valid'         => 'İçe aktarma geçerli değil.',
                'nothing-to-import' => 'İçe aktarılacak kaynak yok.',
                'setup-queue-error' => 'Lütfen içe aktarma işlemini başlatmak için kuyruk sürücüsünü "database" veya "redis" olarak değiştirin.',
                'update-success'    => 'İçe aktarma başarıyla güncellendi.',
            ],
        ],
    ],

    'configuration' => [
        'save-success' => 'Yapılandırma başarıyla kaydedildi.',
    ],
];
