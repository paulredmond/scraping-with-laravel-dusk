<?php

namespace GoutteDemo\Console\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkAnalysisCommand extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->input->getOption('dofollow') && $this->input->getOption('nofollow')) {
            throw new \Exception('You cannot possibly use --nofollow and --dofollow together.');
        }
        $crawler = $this->client->request('GET', $input->getArgument('url'));
        $uri = $crawler->getUri();

        if ($this->input->getOption('nofollow')) {
            $rule = 'a[rel=nofollow]';
        } else if ($this->input->getOption('dofollow')) {
            $rule = 'a:not([rel="nofollow"])';
        } else {
            $rule = 'a';
        }

        $links = $this->getLinks($crawler, $rule);
        $this->output->writeln("Links for {$uri}...({$links->count()})");
        $links->each(function ($link) {
            $this->output->writeln($link);
        });
    }
    
    /**
     * @param $url
     * @return \Illuminate\Support\Collection
     */
    private function getLinks($crawler, $rule)
    {
        $links = new Collection();
        $nodes = $crawler->filter($rule);
        $nodes->each(function ($node) use ($links) {
            $href = $node->attr('href');
            if (preg_match('#^(https?://|/)#i', $href)) {
                $links->push($href);
            }
        });

        return $links;
    }

    protected function configure()
    {
        $this
            ->setName('link:analyze')
            ->setDescription('Analyze links on a given webpage')
            ->setHelp('This command demonstrates analyzing links');

        $this->addArgument('url', InputArgument::REQUIRED, 'The URL to analyze');
        $this->addOption('nofollow', null, InputOption::VALUE_NONE, 'Print out rel="nofollow" Links');
        $this->addOption('dofollow', null, InputOption::VALUE_NONE, 'Print out rel="nofollow" Links');
    }
}
