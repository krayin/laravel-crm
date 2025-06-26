<?php

namespace Webkul\Core\Helpers;

class PermissionHelper
{
    // NOTE: This helper functions as a filter that will tell which option/checkbox will be allowed to be shown on the user interface
    public static function getBaseRolePermissions($allPermissions)
    {
        $allowed = [
            // Dashboard
            "dashboard",

            // Leads
            "leads",
            "leads.create",
            "leads.view",
            "leads.edit",
            "leads.delete",

            // Quotes (Com todas as sub-permissões)
            // "quotes",
            // "quotes.create",
            // "quotes.edit",
            // "quotes.print",
            // "quotes.delete",

            // Mail (Com todas as sub-permissões)
            // "mail",
            // "mail.inbox",
            // "mail.draft",
            // "mail.outbox",
            // "mail.sent",
            // "mail.trash",
            // "mail.compose",
            // "mail.view",
            // "mail.edit",
            // "mail.delete",

            // Activities (Com todas as sub-permissões)
            "activities",
            "activities.create",
            "activities.edit",
            "activities.delete",

            // Contacts Persons
            "contacts",
            "contacts.persons",
            "contacts.persons.create",
            "contacts.persons.edit",
            "contacts.persons.delete",
            "contacts.persons.view",

            // Contacts Organizations
            // "contacts.organizations",
            // "contacts.organizations.create",
            // "contacts.organizations.edit",
            // "contacts.organizations.delete",

            // Products (Com todas as sub-permissões)
            // "products",
            // "products.create",
            // "products.edit",
            // "products.delete",
            // "products.view",

            // Settings
            "settings",

            // Settings → User
            "settings.user",
            "settings.user.groups",
            "settings.user.groups.create",
            "settings.user.groups.edit",
            "settings.user.groups.delete",

            // "settings.user.roles",
            // "settings.user.roles.create",
            // "settings.user.roles.edit",
            // "settings.user.roles.delete",
            
            "settings.user.users",
            "settings.user.users.create",
            "settings.user.users.edit",
            "settings.user.users.delete",

            // Settings → Lead
            "settings.lead",
            // "settings.lead.pipelines",
            // "settings.lead.pipelines.create",
            // "settings.lead.pipelines.edit",
            // "settings.lead.pipelines.delete",
            // "settings.lead.sources",
            // "settings.lead.sources.create",
            // "settings.lead.sources.edit",
            // "settings.lead.sources.delete",
            "settings.lead.types",
            "settings.lead.types.create",
            "settings.lead.types.edit",
            "settings.lead.types.delete",

            // Settings → Automation (Tudo aqui estará comentado, conforme o seu exemplo original)
            // "settings.automation",
            // "settings.automation.attributes",
            // "settings.automation.attributes.create",
            // "settings.automation.attributes.edit",
            // "settings.automation.attributes.delete",
            // "settings.automation.webhooks",
            // "settings.automation.webhooks.create",
            // "settings.automation.webhooks.edit",
            // "settings.automation.webhooks.delete",
            // "settings.automation.workflows",
            // "settings.automation.workflows.create",
            // "settings.automation.workflows.edit",
            // "settings.automation.workflows.delete",
            // "settings.automation.events",
            // "settings.automation.events.create",
            // "settings.automation.events.edit",
            // "settings.automation.events.delete",
            // "settings.automation.campaigns",
            // "settings.automation.campaigns.create",
            // "settings.automation.campaigns.edit",
            // "settings.automation.campaigns.delete",
            // "settings.automation.email_templates",
            // "settings.automation.email_templates.create",
            // "settings.automation.email_templates.edit",
            // "settings.automation.email_templates.delete",

            // Settings -> Other Settings (Incluído e não comentado, pois não estava comentado no seu exemplo)
            "settings.other_settings",
            "settings.other_settings.tags",
            "settings.other_settings.tags.create",
            "settings.other_settings.tags.edit",
            "settings.other_settings.tags.delete",

            // Settings -> Data Transfer (Incluído e não comentado, pois não estava comentado no seu exemplo)
            "settings.data_transfer",
            "settings.data_transfer.imports",
            "settings.data_transfer.imports.create",
            "settings.data_transfer.imports.edit",
            "settings.data_transfer.imports.delete",
            "settings.data_transfer.imports.import",

            // Configuration
            // "configuration",
        ];
        
        $itemsArray = is_iterable($allPermissions)
            ? collect($allPermissions)->values()->all()
            : [];

        return self::filterRecursive($itemsArray, $allowed);
    }

    private static function filterRecursive(array $items, array $allowed): array
    {
        $filtered = [];

        foreach ($items as $item) {
            if (!isset($item->key)) {
                continue;
            }

            $children = isset($item->children) && is_iterable($item->children)
                ? self::filterRecursive(collect($item->children)->values()->all(), $allowed)
                : [];

            if (in_array($item->key, $allowed) || !empty($children)) {
                $filtered[] = [
                    'key'      => $item->key,
                    'name'     => $item->name,
                    'children' => $children,
                ];
            }
        }

        return $filtered;
    }
}


