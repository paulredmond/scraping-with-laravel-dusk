<?php

namespace App\Console\Commands;

use App\Browser;
use Illuminate\Console\Command;

class BrowserDownloadExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'browser:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example command that downloads a file and processes it.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Browser $browser)
    {
        parent::__construct();

        $this->browser = $browser;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->browser->browse(function ($browser) {
            $browser->visit('https://support.spatialkey.com/spatialkey-sample-csv-data/');
            $browser->element('a[href="http://samplecsvs.s3.amazonaws.com/SalesJan2009.csv"]')->click();
            $browser->waitUsing(10, 1, function () {
                $file = $this->browser->downloadsManager->firstMatching(function ($downloadedFile) {
                    return $downloadedFile->getExtension() === 'csv';
                });

                return !is_null($file);
            });

            $csv = $this->browser->downloadsManager->firstMatching(function ($file) {
                return $file->getExtension() == 'csv';
            });
        });
    }
}
