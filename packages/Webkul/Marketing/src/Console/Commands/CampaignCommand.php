<?php

namespace Webkul\Marketing\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Marketing\Helpers\Campaign;

class CampaignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process campaigns and send emails to the contact persons.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected Campaign $campaignHelper)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting campaign processing...');

        try {
            $this->campaignHelper->process();

            $this->info('✅ Campaign processing completed successfully!');
        } catch (\Exception $e) {
            $this->error('❌ An error occurred during campaign processing: '.$e->getMessage());
        }
    }
}
