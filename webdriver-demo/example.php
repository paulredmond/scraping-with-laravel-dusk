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

try {
    // Start chromedriver
    $chromeProcess = buildChromeProcess();
    $chromeProcess->start();

    // Create a RemoteWebDriver Instance
    $driver = driver();

    // Do automation...
    $driver->get('https://github.com');

    // Example of a timeout triggering an exception...
    // $driver->wait(10)->until(
    //   Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
    //     Facebook\WebDriver\WebDriverBy::className('foo-bar-baz')
    //   )
    // );
    
    // Wait for homepage
    $driver->wait(10)->until(
      Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
        Facebook\WebDriver\WebDriverBy::className('site-header-link')
      )
    );
    
    $driver->findElement(Facebook\WebDriver\WebDriverBy::className("site-header-toggle"))->click();
    // Case Matters! This will fail.
    // $driver->findElement(Facebook\WebDriver\WebDriverBy::partialLinkText("Sign In"))->click();
    $driver->findElement(Facebook\WebDriver\WebDriverBy::partialLinkText("Sign in"))->click();

    // Wait for login page
    $driver->wait(10)->until(
      Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
        Facebook\WebDriver\WebDriverBy::className('auth-form-body')
      )
    );

    // Login...
    // Username
    $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('input[name="login"]'))->click();
    $driver->getKeyboard()->sendKeys('user@example.com');

    // Password
    $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('input[name=password]'))->click();
    $driver->getKeyboard()->sendKeys('secret');

    // Try to sign in
    $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('input[type=submit][value="Sign in"]'))->click();

    // Output the error message
    $driver->wait(10)->until(
      Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
        Facebook\WebDriver\WebDriverBy::id('js-flash-container')
      )
    );
    
    $text = $driver->findElement(
        Facebook\WebDriver\WebDriverBy::cssSelector('#js-flash-container div.container')
    )->getText();

    $text = wordwrap($text);

    echo str_repeat("*", 50), "\n";
    echo $text, "\n";
    echo str_repeat("*", 50), "\n\n";
    
} catch(Facebook\WebDriver\Exception\TimeOutException $timeout) {
    echo "Browser timed out waiting for an element.";
} catch (\Exception $e) {
    echo $e->getMessage();
    // $driver->quit();
} finally {
    $chromeProcess->stop();
}

