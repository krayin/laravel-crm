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
        // TODO: Hide the admin role on frontend and disable the option to edit/delete roles 2 and 3
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

                // Settings → Leads (somente types)
                "settings.lead",
                "settings.lead.types",
                "settings.lead.types.create",
                "settings.lead.types.edit",
                "settings.lead.types.delete",

                // Configuration
                "configuration",
            ],
        ]);

        Role::create([
            'id'              => 3, 
            'name'            => trans('installer::app.seeders.user.role.agent', [], $defaultLocale), // ou 'Agente' diretamente
            'description'     => trans('installer::app.seeders.user.role.agent-role', [], $defaultLocale), // ou 'Função base para agentes'
            'permission_type' => 'custom',
            'permissions' => [
                // Dashboard - Agentes geralmente precisam ver o dashboard
                "dashboard",
        
                // Leads - Foco principal do agente
                "leads",
                "leads.create",
                "leads.view",
                "leads.edit",
                // Agentes geralmente não devem ter permissão para deletar leads sem supervisão
                // "leads.delete",
        
                // Contacts (Pessoas) - Agentes precisam gerenciar contatos de pessoas
                "contacts",
                "contacts.persons",
                "contacts.persons.create",
                "contacts.persons.edit",
                "contacts.persons.view",
                // Agentes geralmente não devem ter permissão para deletar contatos sem supervisão
                // "contacts.persons.delete",
        
                // Um agente não deve ter acesso às configurações gerais ou de usuário/grupos/papéis
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
        
                // Agentes não devem ter acesso para modificar configurações de leads (como tipos)
                // "settings.lead",
                // "settings.lead.types",
                // "settings.lead.types.create",
                // "settings.lead.types.edit",
                // "settings.lead.types.delete",
        
                // Agentes não devem ter acesso à configuração geral do sistema
                // "configuration",
            ],
        ]);
    }
}
