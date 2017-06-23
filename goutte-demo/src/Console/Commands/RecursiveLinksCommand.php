<?php

namespace GoutteDemo\Console\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecursiveLinksCommand extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $links = $this->getLinksRecursive($url, 0, 1);
        $links->each(function ($link) use ($output) {
            $output->writeln("Links for {$link['url']}...({$link['children']->count()})");
            $link['children']->each(function ($childUrl) use ($output) {
                $this->output->writeln("\t{$childUrl['url']}");
            });
        });
    }

    private function getLinksRecursive($url, $level = 0, $deep = 3)
    {
        $this->output->writeln("Getting links for {$url}");
        $links = $this->getLinks($url);
        if ($level == $deep) {
            return $links;
        }

        return $links->map(function ($link) use ($level, $deep) {
            $link['children'] = $this->getLinksRecursive($link['url'], $level + 1, $deep);

            return $link;
        });
    }
    
    /**
     * @param $url
     * @return \Illuminate\Support\Collection
     */
    private function getLinks($url)
    {
        $links = new Collection();
        $crawler = $this->client->request('GET', $url);
        $nodes = $crawler->filter('a[href^="http:"], a[href^="https:"]');
        $nodes->each(function ($node) use ($links, $url) {
            $links->push([
                'url' => $node->attr('href'),
                'referrer' => $url,
            ]);
        });

        return $links;
    }

    protected function configure()
    {
        $this
            ->setName('scrape:recursive')
            ->setDescription('Recursively get URLs from a start page')
            ->setHelp('This command demonstrates printing out a nested list of URLs');

        $this->addArgument('url', InputArgument::REQUIRED, 'The URL to start collecting URLs from');
    }
}
