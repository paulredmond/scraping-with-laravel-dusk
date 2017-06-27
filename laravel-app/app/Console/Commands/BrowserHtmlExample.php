<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BrowserHtmlExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'browser:html {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example command that downloads the HTML source of a page and logs it.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new \Goutte\Client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = $this->argument('url');
        $this->info("Downloading HTML source for {$url}");
        $crawler = $this->client->request('GET', $url);
        \Log::info("Downloading source of {$url}...");
        \Log::info($crawler->html());
    }
}
