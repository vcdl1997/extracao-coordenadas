<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
     /**
     * Passos para executar
     *
     * Abra a pasta selenium e execute o comando: "java -jar selenium-server-standalone-3.141.59.jar"
     *
     * Após inicializar o Selenium, execute o comando "php artisan dusk"
     * @return void
     */
    public function testBasicExample()
    {
        DuskTestCase::prepare();
    }
}
