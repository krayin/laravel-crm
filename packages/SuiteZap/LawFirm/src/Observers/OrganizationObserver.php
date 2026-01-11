<?php

namespace SuiteZap\LawFirm\Observers;

use Illuminate\Support\Facades\Log;
use SuiteZap\LawFirm\Models\LawOrganizationDetail;

class OrganizationObserver
{
    /**
     * Handle the Organization "saved" event.
     * This fires after both create and update operations.
     *
     * @param mixed $organization
     * @return void
     */
    public function saved($organization)
    {
        Log::info('LawFirm: Tentando salvar Organization ID: ' . $organization->id, request()->all());

        // Check if law_org_details data was submitted
        if (request()->has('law_org_details')) {
            $data = request('law_org_details');

            // Force organization_id to match the saved organization
            $data['organization_id'] = $organization->id;

            // Update or create the law organization details
            LawOrganizationDetail::updateOrCreate(
                ['organization_id' => $organization->id],
                $data
            );
        }
    }

    /**
     * Handle the Organization "deleted" event.
     *
     * @param mixed $organization
     * @return void
     */
    public function deleted($organization)
    {
        LawOrganizationDetail::where('organization_id', $organization->id)->delete();
    }
}
