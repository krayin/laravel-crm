<?php

namespace Webkul\Installer\Database\Seeders\Workflow;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkflowSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('workflows')->delete();

        $now = Carbon::now();

        $defaultLocale = $parameters['locale'] ?? config('app.locale');

        DB::table('workflows')->insert([
            [
                'id'             => 1,
                'name'           => trans('installer::app.seeders.workflow.email-to-participants-after-activity-creation', [], $defaultLocale),
                'description'    => trans('installer::app.seeders.workflow.email-to-participants-after-activity-creation', [], $defaultLocale),
                'entity_type'    => 'activities',
                'event'          => 'activity.create.after',
                'condition_type' => 'and',
                'conditions'     => '[{"value": ["call", "meeting", "lunch"], "operator": "{}", "attribute": "type", "attribute_type": "multiselect"}]',
                'actions'        => '[{"id": "send_email_to_participants", "value": "1"}]',
                'created_at'     => $now,
                'updated_at'     => $now,
            ], [
                'id'             => 2,
                'name'           => trans('installer::app.seeders.workflow.email-to-participants-after-activity-updation', [], $defaultLocale),
                'description'    => trans('installer::app.seeders.workflow.email-to-participants-after-activity-updation', [], $defaultLocale),
                'entity_type'    => 'activities',
                'event'          => 'activity.update.after',
                'condition_type' => 'and',
                'conditions'     => '[{"value": ["call", "meeting", "lunch"], "operator": "{}", "attribute": "type", "attribute_type": "multiselect"}]',
                'actions'        => '[{"id": "send_email_to_participants", "value": "2"}]',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ]);
    }
}
