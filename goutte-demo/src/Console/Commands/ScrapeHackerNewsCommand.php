<?php

namespace GoutteDemo\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeHackerNewsCommand extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crawler = $this->client->request('GET', 'https://news.ycombinator.com/');
        $links = $crawler->filter('a.storylink');
        $links->each(function ($node) use ($output) {
            $output->writeln($node->attr('href'));
        });
    }
    
    protected function configure()
    {
        $this
            ->setName('scrape:hn')
            ->setDescription('A silly fake HackerNews Scraper')
            ->setHelp('This command returns a list of URLs on Hacker News');
    }
}
