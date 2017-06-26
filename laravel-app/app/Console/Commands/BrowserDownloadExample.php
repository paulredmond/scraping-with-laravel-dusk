<?php

namespace App\Console\Commands;

use App\Browser;
use League\Csv\Reader;
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
            $browser->visit('https://www.microsoft.com/en-us/download/details.aspx?id=45485');
            $browser->waitFor('.download-button');
            $browser->element('.download-button')->click();
            $browser->waitUsing(10, 1, function () {
                $file = $this->browser->downloadsManager->firstMatching(function ($downloadedFile) {
                    return $downloadedFile->getExtension() === 'csv';
                });

                return !is_null($file);
            });

            $csv = $this->browser->downloadsManager->firstMatching(function ($file) {
                return $file->getExtension() == 'csv';
            });

            // Process the CSV and output
            $reader = Reader::createFromFileObject($csv->openFile());
            $data = collect($reader->fetchAssoc(0));
            $users = $data->map(function ($user) {
                return $user['Display Name'];
            })->toArray();

            $this->info(implode("\n", $users));
        });
    }
}
