<?php

namespace GoutteDemo\Console\Commands;

use Goutte\Client as GoutteClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /**
     * @var \Goutte\Client
     */
    protected $client;
    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->client = new GoutteClient();
    }
}
