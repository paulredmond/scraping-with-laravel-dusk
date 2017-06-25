<?php 

require __DIR__.'/init.php';

use Laravel\Dusk\Browser;

try {
    // Start chromedriver
    $chromeProcess = buildChromeProcess();
    $chromeProcess->start();

    // Create a RemoteWebDriver Instance
    $driver = driver();
    $browser = new Browser($driver);

    $browser
        ->visit('https://github.com')
        ->click('button.site-header-toggle')
        ->clickLink('Sign in')
        ->waitFor('.auth-form-body')
        ->type('login', 'user@example.com')
        ->type('password', 'secret')
        ->press('Sign in')
        ->waitFor('#js-flash-container');

    $text = $browser->text('#js-flash-container div.container');

    echo str_repeat("*", 50), "\n";
    echo wordwrap($text), "\n";
    echo str_repeat("*", 50), "\n\n";
    
} catch(Facebook\WebDriver\Exception\TimeOutException $timeout) {
    echo "Browser timed out waiting for an element.";
} catch (\Exception $e) {
    echo $e->getMessage();
    // $driver->quit();
} finally {
    $chromeProcess->stop();
}

