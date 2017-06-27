<?php 

require __DIR__.'/init.php';

use Laravel\Dusk\Browser;

try {
    // Start chromedriver
    $chromeProcess = buildChromeProcess();
    $chromeProcess->start();

    // Create a RemoteWebDriver Instance
    $driver = retry(5, function () {
        return driver();
    }, 50);

    // Example Macroable trait usage...
    // This is entirely unnecessary, except to demonstrate the macro feature
    Browser::macro('wordPressLogin', function ($loginUrl, $log, $pwd) {
        $this->visit($loginUrl)
            ->waitFor('#loginform')
            ->type('log', $log)
            ->type('pwd', $pwd)
            ->press('#wp-submit');

        // Important if you want to chain this
        return $this;
    });

    $browser = new Browser($driver);

    $browser
        ->wordPressLogin('http://174.138.38.208/wp-login.php', 'user@example.com', 'secret')
        ->waitFor('#login_error');

    $text = $browser->text('#login_error');

    echo str_repeat("*", 50), "\n";
    echo wordwrap($text), "\n";
    echo str_repeat("*", 50), "\n\n";

} catch(Facebook\WebDriver\Exception\TimeOutException $timeout) {
    echo "Browser timed out waiting for an element.";
} catch (\Exception $e) {
    echo $e->getMessage();
    // $driver->quit();
} finally {
    // Close the browser
    // $driver->quit();
    $chromeProcess->stop();
}

