<?php

return [
    'seeders' => [
        'attributes' => [
            'address'             => 'العنوان',
            'adjustment-amount'   => 'مبلغ التعديل',
            'billing-address'     => 'عنوان الفاتورة',
            'contact-numbers'     => 'أرقام الاتصال',
            'description'         => 'الوصف',
            'discount-amount'     => 'مبلغ الخصم',
            'discount-percent'    => 'نسبة الخصم',
            'emails'              => 'البريد الإلكتروني',
            'expected-close-date' => 'تاريخ الإغلاق المتوقع',
            'expired-at'          => 'تاريخ الانتهاء',
            'grand-total'         => 'المجموع الكلي',
            'lead-value'          => 'قيمة العميل المحتمل',
            'name'                => 'الاسم',
            'organization'        => 'المؤسسة',
            'person'              => 'الشخص',
            'price'               => 'السعر',
            'quantity'            => 'الكمية',
            'sales-owner'         => 'مالك المبيعات',
            'shipping-address'    => 'عنوان الشحن',
            'sku'                 => 'SKU',
            'source'              => 'المصدر',
            'sub-total'           => 'المجموع الفرعي',
            'subject'             => 'الموضوع',
            'tax-amount'          => 'مبلغ الضريبة',
            'title'               => 'العنوان',
            'type'                => 'النوع',
        ],

        'email' => [
            'activity-created'      => 'تم إنشاء النشاط',
            'activity-modified'     => 'تم تعديل النشاط',
            'date'                  => 'التاريخ',
            'new-activity'          => 'لديك نشاط جديد، يرجى العثور على التفاصيل أدناه',
            'new-activity-modified' => 'لديك نشاط جديد معدل، يرجى العثور على التفاصيل أدناه',
            'participants'          => 'المشاركون',
            'title'                 => 'العنوان',
            'type'                  => 'النوع',
        ],

        'lead' => [
            'pipeline' => [
                'default' => 'المسار الافتراضي',

                'pipeline-stages' => [
                    'follow-up'   => 'متابعة',
                    'lost'        => 'مفقود',
                    'negotiation' => 'تفاوض',
                    'new'         => 'جديد',
                    'prospect'    => 'احتمال',
                    'won'         => 'مكتسب',
                ],
            ],

            'source' => [
                'direct'    => 'مباشر',
                'email'     => 'البريد الإلكتروني',
                'phone'     => 'الهاتف',
                'web'       => 'الويب',
                'web-form'  => 'نموذج الويب',
            ],

            'type' => [
                'existing-business' => 'عمل قائم',
                'new-business'      => 'عمل جديد',
            ],
        ],

        'user' => [
            'role' => [
                'administrator-role' => 'دور المسؤول',
                'administrator'      => 'المسؤول',
            ],
        ],

        'workflow' => [
            'email-to-participants-after-activity-updation' => 'إرسال بريد إلكتروني للمشاركين بعد تحديث النشاط',
            'email-to-participants-after-activity-creation' => 'إرسال بريد إلكتروني للمشاركين بعد إنشاء النشاط',
        ],

        'warehouses' => [
            'contact-name'    => 'اسم جهة الاتصال',
            'contact-emails'  => 'البريد الإلكتروني لجهة الاتصال',
            'contact-numbers' => 'أرقام جهة الاتصال',
            'contact-address' => 'عنوان جهة الاتصال',
            'description'     => 'الوصف',
            'name'            => 'الاسم',
        ],
    ],
];