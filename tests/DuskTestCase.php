<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\TestCase as BaseTestCase;
use App\Services\MapaService;
use Facebook\WebDriver\WebDriverKeys;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        $entidades = MapaService::obterListaDeEnderecos();

        $host = "http://localhost:4444/wd/hub";
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

        $enderecosComCoordenadas = [];

        foreach($entidades as $entidade){
            $driver->get('https://www.google.com.br/maps/');

            $driver->findElement(WebDriverBy::id('searchboxinput'))
                ->sendKeys($entidade['endereco'])
                ->sendKeys(WebDriverKeys::ENTER)
            ;

            $url = $driver->getCurrentURL();

            while($url == "https://www.google.com.br/maps/"){
                $url = $driver->getCurrentURL();
            }

            $coords = explode("@", $url)[1];
            $coords = explode("/", $coords)[0];
            $coords = array_filter(explode(",", $coords), function($coord){
                if(is_numeric($coord)) return $coord;
            });

            list($latitude, $longitude) = $coords;

            $dadosConcatenados = "{$entidade['ejfid']}, {$entidade['enjdsc']}, {$entidade['endid']}, {$entidade['endereco']}, {$latitude}, {$longitude}";

            $enderecosComCoordenadas[] = $dadosConcatenados;
        }

        $csv = fopen('enderecos-com-coordenadas.csv','w');
        fwrite($csv, implode("\n", $enderecosComCoordenadas));
        fclose($csv);
        die();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }
}
