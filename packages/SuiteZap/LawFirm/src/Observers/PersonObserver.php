<?php

namespace SuiteZap\LawFirm\Observers;

use Illuminate\Support\Facades\Log;
use SuiteZap\LawFirm\Models\LawPersonDetail;

class PersonObserver
{
    /**
     * Handle the Person "saved" event.
     * This fires after both create and update operations.
     *
     * @param mixed $person
     * @return void
     */
    public function saved($person)
    {
        Log::info('LawFirm: Tentando salvar Person ID: ' . $person->id, request()->all());

        // Check if law_details data was submitted
        if (request()->has('law_details')) {
            $data = request('law_details');

            // Force person_id to match the saved person
            $data['person_id'] = $person->id;

            // Default type forced to PF if not present (since we split logic)
            if (!isset($data['type'])) {
                $data['type'] = 'PF'; // Legacy support or enforcement
            }

            // Update or create the law person details
            LawPersonDetail::updateOrCreate(
                ['person_id' => $person->id],
                $data
            );
        }
    }

    /**
     * Handle the Person "deleted" event.
     * The cascade on the FK will handle deletion, but we can be explicit.
     *
     * @param mixed $person
     * @return void
     */
    public function deleted($person)
    {
        LawPersonDetail::where('person_id', $person->id)->delete();
    }
}
