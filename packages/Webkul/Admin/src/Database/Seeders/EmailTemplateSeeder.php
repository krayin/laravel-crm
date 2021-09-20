<?php

namespace Webkul\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{

    public function run()
    {
        DB::table('email_templates')->delete();
        
        $now = Carbon::now();

        DB::table('email_templates')->insert([
            [
                'id'         => 1,
                'name'       => 'Activity created',
                'subject'    => 'Activity created: {%activities.title%}',
                'created_at' => $now,
                'updated_at' => $now,
                'content'    => '<p style="font-size: 16px; color: #5e5e5e;">You have a new activity, please find the details bellow:</p>
                                <p><strong style="font-size: 16px;">Details</strong></p>
                                <table style="height: 97px; width: 952px;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">Title</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.title%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">Type</td>
                                                <td style="width: 770.047px; font-size: 16px;">{%activities.type%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">Date</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;">Participants</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.participants%}</td>
                                        </tr>
                                    </tbody>
                                </table>',
            ], [
                'id'         => 2,
                'name'       => 'Activity modified',
                'subject'    => 'Activity modified: {%activities.title%}',
                'created_at' => $now,
                'updated_at' => $now,
                'content'    => '<p style="font-size: 16px; color: #5e5e5e;">This activity has been modified, please find the details bellow:</p>
                                <p><strong style="font-size: 16px;">Details</strong></p>
                                <table style="height: 97px; width: 952px;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">Title</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.title%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">Type</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.type%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px;">Date</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;">Participants</td>
                                            <td style="width: 770.047px; font-size: 16px;">{%activities.participants%}</td>
                                        </tr>
                                    </tbody>
                                </table>',
            ]
        ]);
    }
}