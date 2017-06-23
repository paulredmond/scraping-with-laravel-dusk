#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \GoutteDemo\Console\Commands\ScrapeHackerNewsCommand());
$application->add(new \GoutteDemo\Console\Commands\RecursiveLinksCommand());
$application->add(new \GoutteDemo\Console\Commands\LinkAnalysisCommand());
$application->run();
