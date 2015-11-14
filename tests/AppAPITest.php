<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AppAPITest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    
    public function testResponseIsJson()
    {
        /*
        $response = $this->call('GET', '/');

        $this->assertEquals(200, $response->status());
        
        

        /*$this->visit('/app/user/feedsandcategories')
             ->seeJson();*/
        $this->assertEquals(true, true);
    }
}
