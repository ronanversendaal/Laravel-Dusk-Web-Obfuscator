<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Browser;
use Cocur\BackgroundProcess\BackgroundProcess;
use Illuminate\Support\Facades\Log;

class RunScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:run 
                                {--i|infinite : Whether to ignore term count and run forever } 
                                {--pp|per-page=10 : How many results to display in results } 
                                {--term_count=10 : How many search terms to process }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the scraper as an obfuscator';


    protected $chrome;

    /**
     * 
     */
    private $browser;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->startHeadlessChrome();

        
        $this->browser = new Browser();

        
    }

    public function __destruct(){
        if($this->chrome->isRunning()){
            $this->chrome->stop();
        }
    }

    private function startHeadlessChrome()
    {
        $this->chrome =  new BackgroundProcess('vendor/laravel/dusk/bin/chromedriver-linux');
        $this->chrome->run();
    }

    /**
     * Sign into chrome to allow search results to be included in your profile.
     * 
     * Needs your credentials which can be placed in .env
     */
    public function loginToChrome($browser)
    {
        $this->alert('Logging into Chrome');

        $browser->visit('https://accounts.google.com/Login')->assertSee('Sign in');

        $browser->keys('#Email', env('CHROME_IDENTIFIER'), ['{enter}']);
        $browser->keys('#Passwd', env('CHROME_PASSWORD'), ['{enter}']);

        $browser->assertSee('Google-account');

        $this->info('Succesfully logged in');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = $this->options();
        $arguments = $this->arguments();

        $this->browser->browse(function ($browser) use ($options){

            $this->loginToChrome($browser);

            // This wordlist contains thousands of random words that can obfuscate your search history.
            $wordlist = \Storage::get('wordlist.json');
            $search_terms = json_decode($wordlist, true);

            if(!$this->option('infinite')){
                // Pick a set of random words
                $search_terms = array_rand($search_terms, $this->option('term_count'));
            } else { 
                $search_terms = array_keys($search_terms);
                shuffle($search_terms);
            }

            activity()->log('Starting obfuscator with the following terms: '.implode(', ', array_slice($search_terms, 0, $this->option('term_count'))));

            // Amount of search results to visit.
            $per_page = $this->option('per-page');
            $url_google = 'https://google.com?num='.$per_page;

            foreach ($search_terms as $query) {

                // Start entering the query
                try{

                    $browser->visit($url_google);
                    $result = $browser->assertSee('Google');
                    
                    // Begin looping through keywords here. @todo check to move in the whenAvailable block
                    $browser->clear('#lst-ib');
                    $browser->keys('#lst-ib', $query, ['{enter}']);

                    $browser->whenAvailable('#search', function () use ($browser, $per_page, $query) {

                        // Store the current result page.
                        $begin_url = $browser->driver->getCurrentUrl();

                        activity()->withProperties(['level' => 'info'])->log('Getting results for: '. $query);
                        
                        $tries = 0;
                        // Loop through the available results

                        for ($i = 1; $i<=$per_page; $i++) {

                                if($tries <= 5){
                                    // $browser->assertPresent('.g');

                                    $element = $browser->element('.g:nth-child('.$i.') .r > a');

                                    // Checks if the result element is clickable so we can continue
                                    if($element){
                                        $element->click();
                                    
                                        $browser->waitUntilMissing('body');

                                        if ($begin_url !== $browser->driver->getCurrentUrl()){

                                            $browser->script('window.scrollTo(0, 500);');

                                            activity()->withProperties(['level' => 'success'])->log('Visiting url '.$i.': '.$browser->driver->getCurrentUrl());

                                            $browser->visit($begin_url)->waitUntilMissing('body');

                                        } else {
                                            activity()->withProperties(['level' => 'alert'])->log('Url currently matches starting url.. Retrying.');
                                        }

                                    } else {
                                        activity()->withProperties(['level' => 'alert'])->log('Element unaccesible. Skipping..');
                                        $this->alert('Element unaccesible. Skipping..');
                                        $tries++;
                                    }    
                                } else {
                                    $tries = 0;
                                    $browser->visit($url_google);
                                }
                            
                                
                        }
                    });
                } catch (\Exception $e){
                    activity()->withProperties(['level' => 'error'])->log('Error thrown while visiting link: '. $e->getMessage());
                }
            }
        });
    }
}
