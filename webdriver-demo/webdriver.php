<?php

require __DIR__.'/init.php';

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
} finally {
    // Close the browser
    // $driver->quit();

    // Stop the chromedriver process
    $chromeProcess->stop();
}

