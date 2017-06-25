<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

function driverSuffix() {
    switch (PHP_OS) {
        case 'Darwin':
            return 'mac';
        case 'WINNT':
            return 'win.exe';
        default:
            return 'linux';
    }
}

function buildChromeProcess() {
    $driver = realpath(__DIR__.'/bin/chromedriver-'.driverSuffix());
    if (realpath($driver) === false) {
        throw new RuntimeException("Invalid path to Chromedriver [{$driver}].");
    }

    $env = ['DISPLAY' => ':0'];
    if (PHP_OS === 'Darwin' || PHP_OS === 'WINNT') {
        $env = [];
    }

    return (new ProcessBuilder())
            ->setPrefix(realpath($driver))
            ->getProcess()
            ->setEnv($env);
}

function driver() {
    $options = (new ChromeOptions)->addArguments([]);

    return RemoteWebDriver::create(
        'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
            ChromeOptions::CAPABILITY, $options
        )
    );
}
