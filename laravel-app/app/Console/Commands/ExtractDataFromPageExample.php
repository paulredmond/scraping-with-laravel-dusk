<?php

namespace App\Console\Commands;

use App\Browser;
use League\Csv\Reader;
use Illuminate\Console\Command;

class ExtractDataFromPageExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'browser:extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract CSV data from the browser DOM';

    /**
     * @var \App\Browser
     */
    protected $browser;

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
            $browser->visit('http://www.basketball-reference.com/teams/PHO/2017.html');
            $browser->script('document.getElementById("content").scrollIntoView()');
            $browser->with('#all_roster', function ($roster) use ($browser) {
                $roster->mouseover('.hasmore > span');
                // Gets a little interesting here...
                $browser->waitFor('div.section_heading > div > ul > li.hasmore > div > ul > li:nth-child(4) > button');
                $roster->element('div.section_heading > div > ul > li.hasmore > div > ul > li:nth-child(4) > button')->click();
            });

            $browser->waitFor('#csv_roster');
            $text = $browser->text('#csv_roster');
            $reader = Reader::createFromString($text);
            $roster = collect($reader->fetchAssoc(0));
            $roster->each(function ($player) {
                $name = explode('\\', $player['Player'])[0];
                $college = empty($player['College']) ? false : $player['College'];
                
                if ($college) {
                    $this->info("{$player['Pos']}, from {$college}, number ${player['No.']}...{$name}!");
                } else {
                    $this->info("{$player['Pos']}, number ${player['No.']}...{$name}!");
                }
            });
        });
    }
}
