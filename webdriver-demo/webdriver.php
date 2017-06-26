<?php

require __DIR__.'/init.php';

try {
    // Start chromedriver
    $chromeProcess = buildChromeProcess();
    $chromeProcess->start();

    // Create a RemoteWebDriver Instance
    $driver = retry(5, function () {
        return driver();
    }, 50);

    // Do automation...
    // This is for demonstration purposes. This will likely not be here when you try this.
    $driver->get('http://174.138.38.208/wp-login.php');

    // Example of a timeout triggering an exception...
    // $driver->wait(10)->until(
    //   Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
    //     Facebook\WebDriver\WebDriverBy::className('foo-bar-baz')
    //   )
    // );
    
    // Wait for homepage
    $driver->wait(10)->until(
      Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
        Facebook\WebDriver\WebDriverBy::id('loginform')
      )
    );

    // Login...
    // Username
    $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('input[name="log"]'))->click();
    $driver->getKeyboard()->sendKeys('user@example.com');

    // Password
    $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('input[name=pwd]'))->click();
    $driver->getKeyboard()->sendKeys('secret');

    // Checkbox Example
    $remember = $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('input[name=rememberme]'));
    if (!$remember->getAttribute('checked')) {
        $remember->click();
    }

    // Try to sign in
    $driver->findElement(Facebook\WebDriver\WebDriverBy::cssSelector('#wp-submit'))->click();

    // Output the error message
    $driver->wait(10)->until(
      Facebook\WebDriver\WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
        Facebook\WebDriver\WebDriverBy::id('login_error')
      )
    );
    
    $text = $driver->findElement(
        Facebook\WebDriver\WebDriverBy::cssSelector('#login_error')
    )->getText();

    $text = wordwrap($text);

    echo str_repeat("*", 50), "\n";
    echo $text, "\n";
    echo str_repeat("*", 50), "\n\n";

} catch(Facebook\WebDriver\Exception\TimeOutException $timeout) {
    echo "Browser timed out waiting for an element.";
} catch (\Exception $e) {
    echo $e->getMessage();
} finally {
    // Close the browser
    // $driver->quit();

    // Stop the chromedriver process
    $chromeProcess->stop();
}

