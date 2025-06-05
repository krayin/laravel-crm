<?php

return [
    'common' => [
        'auth' => [
            'login' => [
                'success' => 'ورود با موفقیت انجام شد.',
                'logout'  => 'خروج با موفقیت انجام شد.',
            ],
        ],

        'resource-not-found'    => 'منبع درخواست‌شده یافت نشد.',
        'forbidden-error'       => 'شما اجازه دسترسی به این منبع را ندارید.',
        'unauthenticated'       => 'شما احراز هویت نشده‌اید. لطفاً برای ادامه، وارد شوید.',
        'internal-server-error' => 'یک خطای غیرمنتظره در سرور رخ داد. لطفاً بعداً دوباره تلاش کنید.',
    ],

    'products' => [
        'create-success'           => 'محصول با موفقیت ایجاد شد.',
        'updated-success'          => 'محصول با موفقیت به‌روزرسانی شد.',
        'delete-success'           => 'محصول با موفقیت حذف شد.',
        'delete-failed'            => 'حذف محصول با شکست مواجه شد.',
        'inventory-create-success' => 'موجودی با موفقیت ذخیره شد.',
    ],

    'leads' => [
        'create-success'  => 'سرنخ با موفقیت ایجاد شد.',
        'updated-success' => 'سرنخ با موفقیت به‌روزرسانی شد.',
        'delete-success'  => 'سرنخ با موفقیت حذف شد.',
        'delete-failed'   => 'حذف سرنخ با شکست مواجه شد.',
        'no-valid-files'  => 'فایل معتبری یافت نشد.',

        'view' => [
            'tags' => [
                'create-success' => 'برچسب با موفقیت پیوست شد.',
                'delete-success' => 'برچسب با موفقیت جدا شد.',
            ],

            'quotes' => [
                'create-success' => 'پیشنهاد قیمت با موفقیت پیوست شد.',
                'delete-success' => 'پیشنهاد قیمت با موفقیت جدا شد.',
            ],
        ],
    ],

    'quotes' => [
        'create-success'  => 'پیشنهاد قیمت با موفقیت ایجاد شد.',
        'update-success'  => 'پیشنهاد قیمت با موفقیت به‌روزرسانی شد.',
        'delete-success'  => 'پیشنهاد قیمت با موفقیت حذف شد.',
        'delete-failed'   => 'حذف پیشنهاد قیمت با شکست مواجه شد.',
        'saved-to-draft'  => 'پیشنهاد قیمت به‌عنوان پیش‌نویس ذخیره شد.',
    ],

    'mail' => [
        'create-success' => 'ایمیل با موفقیت ایجاد شد.',
        'update-success' => 'ایمیل با موفقیت به‌روزرسانی شد.',
        'delete-success' => 'ایمیل با موفقیت حذف شد.',
        'delete-failed'  => 'حذف ایمیل با شکست مواجه شد.',
        'saved-to-draft' => 'ایمیل به‌عنوان پیش‌نویس ذخیره شد.',

        'view' => [
            'tags' => [
                'create-success' => 'برچسب با موفقیت پیوست شد.',
                'delete-success' => 'برچسب با موفقیت جدا شد.',
            ],
        ],
    ],

    'activities' => [
        'create-success' => 'فعالیت با موفقیت ایجاد شد.',
        'update-success' => 'فعالیت با موفقیت به‌روزرسانی شد.',
        'delete-success' => 'فعالیت با موفقیت حذف شد.',
        'delete-failed'  => 'حذف فعالیت با شکست مواجه شد.',
    ],

    'contacts' => [
        'persons' => [
            'create-success'  => 'شخص با موفقیت ایجاد شد.',
            'update-success'  => 'شخص با موفقیت به‌روزرسانی شد.',
            'delete-success'  => 'شخص با موفقیت حذف شد.',
            'delete-failed'   => 'حذف شخص با شکست مواجه شد.',

            'view' => [
                'tags' => [
                    'create-success' => 'برچسب با موفقیت پیوست شد.',
                    'delete-success' => 'برچسب با موفقیت جدا شد.',
                ],
            ],
        ],

        'organizations' => [
            'create-success'  => 'سازمان با موفقیت ایجاد شد.',
            'update-success'  => 'سازمان با موفقیت به‌روزرسانی شد.',
            'delete-success'  => 'سازمان با موفقیت حذف شد.',
            'delete-failed'   => 'حذف سازمان با شکست مواجه شد.',
        ],
    ],

    'settings' => [
        'tags' => [
            'create-success' => 'برچسب با موفقیت ایجاد شد.',
            'update-success' => 'برچسب با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'برچسب با موفقیت حذف شد.',
            'delete-failed'  => 'حذف برچسب با شکست مواجه شد.',
        ],

        'web-forms' => [
            'create-success'  => 'فرم وب با موفقیت ایجاد شد.',
            'updated-success' => 'فرم وب با موفقیت به‌روزرسانی شد.',
            'delete-success'  => 'فرم وب با موفقیت حذف شد.',
            'delete-failed'   => 'حذف فرم وب با شکست مواجه شد.',
        ],

        'attributes' => [
            'create-success'    => 'ویژگی با موفقیت ایجاد شد.',
            'update-success'    => 'ویژگی با موفقیت به‌روزرسانی شد.',
            'destroy-success'   => 'ویژگی با موفقیت حذف شد.',
            'delete-failed'     => 'حذف ویژگی با شکست مواجه شد.',
            'user-define-error' => 'ویژگی تعریف شده توسط کاربر قابل حذف نیست.',
        ],

        'groups' => [
            'create-success'  => 'گروه با موفقیت ایجاد شد.',
            'update-success'  => 'گروه با موفقیت به‌روزرسانی شد.',
            'destroy-success' => 'گروه با موفقیت حذف شد.',
            'delete-failed'   => 'حذف گروه با شکست مواجه شد.',
        ],

        'marketing' => [
            'events' => [
                'create-success'  => 'رویداد بازاریابی با موفقیت ایجاد شد.',
                'update-success'  => 'رویداد بازاریابی با موفقیت به‌روزرسانی شد.',
                'destroy-success' => 'رویداد بازاریابی با موفقیت حذف شد.',
                'delete-failed'   => 'حذف رویداد بازاریابی ناموفق بود.',
            ],

            'campaigns' => [
                'create-success'  => 'کمپین بازاریابی با موفقیت ایجاد شد.',
                'update-success'  => 'کمپین بازاریابی با موفقیت به‌روزرسانی شد.',
                'destroy-success' => 'کمپین بازاریابی با موفقیت حذف شد.',
                'delete-failed'   => 'حذف کمپین بازاریابی ناموفق بود.',
            ],
        ],

        'roles' => [
            'create-success' => 'نقش با موفقیت ایجاد شد.',
            'update-success' => 'نقش با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'نقش با موفقیت حذف شد.',
            'delete-failed'  => 'حذف نقش با شکست مواجه شد.',
        ],

        'users' => [
            'create-success'      => 'کاربر با موفقیت ایجاد شد.',
            'updated-success'     => 'کاربر با موفقیت به‌روزرسانی شد.',
            'delete-success'      => 'کاربر با موفقیت حذف شد.',
            'delete-failed'       => 'حذف کاربر با شکست مواجه شد.',
            'last-delete-error'   => 'حداقل یک کاربر مورد نیاز است.',
            'mass-delete-success' => 'کاربران انتخاب‌شده با موفقیت حذف شدند.',
            'mass-delete-failed'  => 'حذف کاربران انتخاب‌شده با شکست مواجه شد.',
            'mass-update-success' => 'کاربران انتخاب‌شده با موفقیت به‌روزرسانی شدند.',
            'mass-update-failed'  => 'به‌روزرسانی کاربران انتخاب‌شده با شکست مواجه شد.',
        ],

        'pipelines' => [
            'create-success'       => 'پایپ‌لاین با موفقیت ایجاد شد.',
            'updated-success'      => 'پایپ‌لاین با موفقیت به‌روزرسانی شد.',
            'delete-success'       => 'پایپ‌لاین با موفقیت حذف شد.',
            'default-delete-error' => 'پایپ‌لاین پیش‌فرض قابل حذف نیست.',
        ],

        'sources' => [
            'create-success' => 'منبع با موفقیت ایجاد شد.',
            'update-success' => 'منبع با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'منبع با موفقیت حذف شد.',
            'delete-failed'  => 'حذف منبع با شکست مواجه شد.',
        ],

        'types' => [
            'create-success' => 'نوع با موفقیت ایجاد شد.',
            'update-success' => 'نوع با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'نوع با موفقیت حذف شد.',
            'delete-failed'  => 'حذف نوع با شکست مواجه شد.',
        ],

        'email-templates' => [
            'create-success' => 'قالب ایمیل با موفقیت ایجاد شد.',
            'update-success' => 'قالب ایمیل با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'قالب ایمیل با موفقیت حذف شد.',
            'delete-failed'  => 'حذف قالب ایمیل با شکست مواجه شد.',
        ],

        'workflows' => [
            'create-success' => 'گردش کار با موفقیت ایجاد شد.',
            'update-success' => 'گردش کار با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'گردش کار با موفقیت حذف شد.',
            'delete-failed'  => 'حذف گردش کار با شکست مواجه شد.',
        ],

        'warehouses' => [
            'create-success' => 'مستودع با موفقیت ایجاد شد.',
            'update-success' => 'مستودع با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'مستودع با موفقیت حذف شد.',
            'delete-failed'  => 'حذف مستودع ناموفق بود.',

            'view' => [
                'locations' => [
                    'create-success' => 'موقعیت با موفقیت ایجاد شد.',
                    'update-success' => 'موقعیت با موفقیت به‌روزرسانی شد.',
                    'delete-success' => 'موقعیت با موفقیت حذف شد.',
                    'delete-failed'  => 'حذف موقعیت ناموفق بود.',
                ],

                'tags' => [
                    'create-success' => 'برچسب با موفقیت متصل شد.',
                    'delete-success' => 'برچسب با موفقیت جدا شد.',
                ],
            ],
        ],

        'webhooks' => [
            'create-success' => 'وب‌هوک با موفقیت ایجاد شد.',
            'update-success' => 'وب‌هوک با موفقیت به‌روزرسانی شد.',
            'delete-success' => 'وب‌هوک با موفقیت حذف شد.',
            'delete-failed'  => 'حذف وب‌هوک ناموفق بود.',
        ],

        'data-transfer' => [
            'imports' => [
                'create-success'    => 'واردات با موفقیت ایجاد شد.',
                'delete-failed'     => 'حذف واردات به‌طور غیرمنتظره‌ای با شکست مواجه شد.',
                'delete-success'    => 'واردات با موفقیت حذف شد.',
                'not-valid'         => 'واردات نامعتبر است.',
                'nothing-to-import' => 'منابعی برای وارد کردن وجود ندارد.',
                'setup-queue-error' => 'لطفاً درایور صف خود را به "database" یا "redis" تغییر دهید تا فرایند واردات آغاز شود.',
                'update-success'    => 'واردات با موفقیت به‌روزرسانی شد.',
            ],
        ],

        'configuration' => [
            'save-success' => 'پیکربندی با موفقیت ذخیره شد.',
        ],
    ],
];
