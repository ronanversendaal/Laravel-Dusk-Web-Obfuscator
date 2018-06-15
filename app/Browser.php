<?php namespace App;

use Closure;
use Laravel\Dusk\Browser as DuskBrowser;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class Browser
{

    /**
     * @var \Laravel\Dusk\Browser
     */
    private $browser;

    /**
     * 
     */
    public function browse(Closure $callback)
    {
        if (!$this->browser) {
            $this->browser = $this->newBrowser($this->createWebDriver());
        }
        try{
            $callback($this->browser);
        } catch (Exception $e){
            throw $e;
        } catch (Throwable $e){
            throw $e;
        }
    }

    /**
     * 
     */
    function __destruct()
    {
        if ($this->browser) {
            $this->closeBrowser();
        }
    }

    public function closeBrowser()
    {
        if (!$this->browser) {
            throw new Exception('Browser has not been initialised yet.');
        }

        $this->browser->quit();
        $this->browser = null;
    }

    /**
     * 
     */
    protected function newBrowser($driver)
    {
        return new DuskBrowser($driver);
    }

    protected function createWebDriver()
    {
        return retry(5, function () {
            return $this->driver();
        }, 50);
    }

    protected function driver() {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--no-sandbox'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $driver = RemoteWebDriver::create(
            'http://localhost:9515', $capabilities
        );

        return $driver;
    }

}
