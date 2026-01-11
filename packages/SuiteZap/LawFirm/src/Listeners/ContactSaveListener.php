<?php

namespace SuiteZap\LawFirm\Listeners;

use SuiteZap\LawFirm\Models\LawPersonDetail;
use SuiteZap\LawFirm\Models\LawOrganizationDetail;
use Illuminate\Support\Facades\Log;

class ContactSaveListener
{
    /**
     * Handle Person save (Create/Update).
     * 
     * @param mixed $person
     * @return void
     */
    public function handlePersonSave($person)
    {
        Log::info('LawFirm: Listener handlePersonSave disparado para Person ID: ' . $person->id);

        if (request()->has('law_details')) {
            $data = request('law_details');
            $data['person_id'] = $person->id;

            // Default to PF if not present
            if (!isset($data['type'])) {
                $data['type'] = 'PF';
            }

            LawPersonDetail::updateOrCreate(
                ['person_id' => $person->id],
                $data
            );

            Log::info('LawFirm: Dados de Pessoa (PF) salvos com sucesso via Listener.');
        } else {
            Log::info('LawFirm: Nenhum dado law_details encontrado no request.');
        }
    }

    /**
     * Handle Organization save (Create/Update).
     * 
     * @param mixed $organization
     * @return void
     */
    public function handleOrganizationSave($organization)
    {
        Log::info('LawFirm: Listener handleOrganizationSave disparado para Organization ID: ' . $organization->id);

        if (request()->has('law_org_details')) {
            $data = request('law_org_details');
            $data['organization_id'] = $organization->id;

            LawOrganizationDetail::updateOrCreate(
                ['organization_id' => $organization->id],
                $data
            );

            Log::info('LawFirm: Dados de Organização (PJ) salvos com sucesso via Listener.');
        } else {
            Log::info('LawFirm: Nenhum dado law_org_details encontrado no request.');
        }
    }
}
