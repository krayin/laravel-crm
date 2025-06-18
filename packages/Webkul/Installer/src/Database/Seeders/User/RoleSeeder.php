<?php

namespace Webkul\Installer\Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\User\Models\Role;

class RoleSeeder extends Seeder
{
    public function run($parameters = [])
    {
        DB::table('users')->delete();
        DB::table('roles')->delete();

        $defaultLocale = $parameters['locale'] ?? config('app.locale');
        //! Creating 3 standard roles that does not have a specified tenant and will not be able to be deleted or edited
        // NOTE: Role 1 is expected to be the admin role that only developers will have access to, so it should be hidden
        // NOTE: Role 2 is manager role
        // NOTE: Role 3 is agent role, it does not have delete functions and access to settings
        Role::create([
            'id'              => 1,
            'name'            => trans('installer::app.seeders.user.role.administrator', [], $defaultLocale),
            'description'     => trans('installer::app.seeders.user.role.administrator-role', [], $defaultLocale),
            'permission_type' => 'all',
        ]);

        Role::create([
            'id'              => 2,
            'name'            => trans('installer::app.seeders.user.role.manager', [], $defaultLocale),
            'description'     => trans('installer::app.seeders.user.role.manager-role', [], $defaultLocale),
            'permission_type' => 'custom',
            'permissions' => [
                "dashboard",
                "leads",
                "leads.create",
                "leads.view",
                "leads.edit",
                "leads.delete",
                "activities",
                "activities.create",
                "activities.edit",
                "activities.delete",
                "contacts",
                "contacts.persons",
                "contacts.persons.create",
                "contacts.persons.edit",
                "contacts.persons.delete",
                "contacts.persons.view",
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
                "settings.lead",
                "settings.lead.sources",
                "settings.lead.sources.create",
                "settings.lead.sources.edit",
                "settings.lead.sources.delete",
                "settings.lead.types",
                "settings.lead.types.create",
                "settings.lead.types.edit",
                "settings.lead.types.delete",
                "settings.other_settings",
                "settings.other_settings.tags",
                "settings.other_settings.tags.create",
                "settings.other_settings.tags.edit",
                "settings.other_settings.tags.delete",
                "settings.data_transfer",
                "settings.data_transfer.imports",
                "settings.data_transfer.imports.create",
                "settings.data_transfer.imports.edit",
                "settings.data_transfer.imports.delete",
                "settings.data_transfer.imports.import",
                "configuration",
            ],
        ]);

        Role::create([
            'id'              => 3,
            'name'            => trans('installer::app.seeders.user.role.agent', [], $defaultLocale), // ou 'Agente' diretamente
            'description'     => trans('installer::app.seeders.user.role.agent-role', [], $defaultLocale), // ou 'Função base para agentes'
            'permission_type' => 'custom',
            'permissions' => [
                "dashboard",
                "leads",
                "leads.create",
                "leads.view",
                "leads.edit",
                // "leads.delete",
                "activities",
                "activities.create",
                "activities.edit",
                // "activities.delete",
                "contacts",
                "contacts.persons",
                "contacts.persons.create",
                "contacts.persons.edit",
                // "contacts.persons.delete",
                "contacts.persons.view",
                // "settings",
                // "settings.user",
                // "settings.user.groups",
                // "settings.user.groups.create",
                // "settings.user.groups.edit",
                // "settings.user.groups.delete",
                // "settings.user.roles",
                // "settings.user.roles.create",
                // "settings.user.roles.edit",
                // "settings.user.roles.delete",
                // "settings.user.users",
                // "settings.user.users.create",
                // "settings.user.users.edit",
                // "settings.user.users.delete",
                // "settings.lead",
                // "settings.lead.sources",
                // "settings.lead.sources.create",
                // "settings.lead.sources.edit",
                // "settings.lead.sources.delete",
                // "settings.lead.types",
                // "settings.lead.types.create",
                // "settings.lead.types.edit",
                // "settings.lead.types.delete",
                // "settings.other_settings",
                // "settings.other_settings.tags",
                // "settings.other_settings.tags.create",
                // "settings.other_settings.tags.edit",
                // "settings.other_settings.tags.delete",
                // "settings.data_transfer",
                // "settings.data_transfer.imports",
                // "settings.data_transfer.imports.create",
                // "settings.data_transfer.imports.edit",
                // "settings.data_transfer.imports.delete",
                // "settings.data_transfer.imports.import",
                // "configuration",
            ],
        ]);
    }
}