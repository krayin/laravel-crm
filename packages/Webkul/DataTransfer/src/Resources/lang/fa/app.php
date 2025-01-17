<?php

return [
    'importers' => [
        'persons' => [
            'title' => 'افراد',

            'validation' => [
                'errors' => [
                    'duplicate-email' => 'ایمیل: \'%s\' بیش از یک بار در فایل واردات یافت شد.',
                    'duplicate-phone' => 'تلفن: \'%s\' بیش از یک بار در فایل واردات یافت شد.',
                    'email-not-found' => 'ایمیل: \'%s\' در سیستم یافت نشد.',
                ],
            ],
        ],

        'products' => [
            'title' => 'محصولات',

            'validation' => [
                'errors' => [
                    'sku-not-found' => 'محصول با کد SKU مشخص شده یافت نشد.',
                ],
            ],
        ],

        'leads' => [
            'title' => 'سرنخ‌ها',

            'validation' => [
                'errors' => [
                    'id-not-found' => 'شناسه: \'%s\' در سیستم یافت نشد.',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => 'ستون‌های شماره "%s" دارای سرصفحه‌های خالی هستند.',
            'column-name-invalid'  => 'نام‌های ستون نامعتبر: "%s".',
            'column-not-found'     => 'ستون‌های مورد نیاز یافت نشد: %s.',
            'column-numbers'       => 'تعداد ستون‌ها با تعداد سطرهای سرصفحه مطابقت ندارد.',
            'invalid-attribute'    => 'سرصفحه شامل ویژگی‌های نامعتبر است: "%s".',
            'system'               => 'خطای غیرمنتظره‌ای در سیستم رخ داد.',
            'wrong-quotes'         => 'به جای گیومه‌های مستقیم از گیومه‌های خمیده استفاده شده است.',
        ],
    ],
];
