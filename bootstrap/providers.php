<?php

use App\Providers\AppServiceProvider;
use Barryvdh\DomPDF\ServiceProvider;
use Konekt\Concord\ConcordServiceProvider;
use Prettus\Repository\Providers\RepositoryServiceProvider;
use Webkul\Activity\Providers\ActivityServiceProvider;
use Webkul\Admin\Providers\AdminServiceProvider;
use Webkul\Attribute\Providers\AttributeServiceProvider;
use Webkul\Automation\Providers\WorkflowServiceProvider;
use Webkul\Contact\Providers\ContactServiceProvider;
use Webkul\Core\Providers\CoreServiceProvider;
use Webkul\DataGrid\Providers\DataGridServiceProvider;
use Webkul\DataTransfer\Providers\DataTransferServiceProvider;
use Webkul\Email\Providers\EmailServiceProvider;
use Webkul\EmailTemplate\Providers\EmailTemplateServiceProvider;
use Webkul\GoogleContact\Providers\GoogleContactServiceProvider;
use Webkul\Installer\Providers\InstallerServiceProvider;
use Webkul\Lead\Providers\LeadServiceProvider;
use Webkul\Marketing\Providers\MarketingServiceProvider;
use Webkul\Product\Providers\ProductServiceProvider;
use Webkul\Quote\Providers\QuoteServiceProvider;
use Webkul\Tag\Providers\TagServiceProvider;
use Webkul\User\Providers\UserServiceProvider;
use Webkul\Warehouse\Providers\WarehouseServiceProvider;
use Webkul\WebForm\Providers\WebFormServiceProvider;

return [
    /*
     * Package Service Providers...
     */
    ServiceProvider::class,
    ConcordServiceProvider::class,
    RepositoryServiceProvider::class,

    /*
     * Application Service Providers...
     */
    AppServiceProvider::class,

    /*
     * Webkul Service Providers...
     */
    ActivityServiceProvider::class,
    AdminServiceProvider::class,
    AttributeServiceProvider::class,
    WorkflowServiceProvider::class,
    ContactServiceProvider::class,
    CoreServiceProvider::class,
    DataGridServiceProvider::class,
    DataTransferServiceProvider::class,
    EmailTemplateServiceProvider::class,
    EmailServiceProvider::class,
    GoogleContactServiceProvider::class,
    MarketingServiceProvider::class,
    InstallerServiceProvider::class,
    LeadServiceProvider::class,
    ProductServiceProvider::class,
    QuoteServiceProvider::class,
    TagServiceProvider::class,
    UserServiceProvider::class,
    WarehouseServiceProvider::class,
    WebFormServiceProvider::class,
];
