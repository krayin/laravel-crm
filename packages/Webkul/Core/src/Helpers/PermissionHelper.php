<?php

namespace Webkul\Core\Helpers;

class PermissionHelper
{
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
        
            // Contacts
            "contacts",
            "contacts.persons",
            "contacts.persons.create",
            "contacts.persons.edit",
            "contacts.persons.delete",
            "contacts.persons.view",
        
            // Settings → User
            "settings",
            "settings.user",
            "settings.user.groups",
            "settings.user.groups.create",
            "settings.user.groups.edit",
            "settings.user.groups.delete",
            "settings.user.roles",
            "settings.user.roles.create",
            "settings.user.roles.edit",
            "settings.user.roles.delete",
            "settings.user.users",
            "settings.user.users.create",
            "settings.user.users.edit",
            "settings.user.users.delete",
        
            // Settings → Leads
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
        
            // Settings → Automation
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
        
            // Configuration
            "configuration",
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


