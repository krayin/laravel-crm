<?php

namespace Webkul\Installer\Database\Seeders\EmailTemplate;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('email_templates')->delete();

        $now = Carbon::now();

        $defaultLocale = $parameters['locale'] ?? config('app.locale');

        DB::table('email_templates')->insert([
            [
                'id'         => 1,
                'name'       => trans('installer::app.seeders.email.activity-created', [], $defaultLocale),
                'subject'    => trans('installer::app.seeders.email.activity-created', [], $defaultLocale).': {%activities.title%}',
                'created_at' => $now,
                'updated_at' => $now,
                'content'    => '<p style="font-size: 16px; color: #5e5e5e;">'.trans('installer::app.seeders.email.new-activity', [], $defaultLocale).':</p>
                                <p><strong style="font-size: 16px;">Details</strong></p>
                                <table style="height: 97px; width: 952px;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">'.trans('installer::app.seeders.email.title', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.title%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">'.trans('installer::app.seeders.email.type', [], $defaultLocale).'</td>
                                                <td style="width: 770.047px; font-size: 16px;">{%activities.type%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">'.trans('installer::app.seeders.email.date', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;">'.trans('installer::app.seeders.email.participants', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.participants%}</td>
                                        </tr>
                                    </tbody>
                                </table>',
            ], [
                'id'         => 2,
                'name'       => trans('installer::app.seeders.email.activity-modified', [], $defaultLocale),
                'subject'    => trans('installer::app.seeders.email.activity-modified', [], $defaultLocale).': {%activities.title%}',
                'created_at' => $now,
                'updated_at' => $now,
                'content'    => '<p style="font-size: 16px; color: #5e5e5e;">'.trans('installer::app.seeders.email.new-activity-modified', [], $defaultLocale).':</p>
                                <p><strong style="font-size: 16px;">Details</strong></p>
                                <table style="height: 97px; width: 952px;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">'.trans('installer::app.seeders.email.title', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.title%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">'.trans('installer::app.seeders.email.type', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.type%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">'.trans('installer::app.seeders.email.date', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;">'.trans('installer::app.seeders.email.participants', [], $defaultLocale).'</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.participants%}</td>
                                        </tr>
                                    </tbody>
                                </table>',
            ],
        ]);
    }
}
